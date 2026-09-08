<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\FormationRegistration;
use App\Mail\FormationRegistrationConfirmation;
use App\Services\GeniusPayService;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class FormationController extends Controller
{
    public function __construct(
        private GeniusPayService $geniusPay,
        private TelegramService  $telegram,
    ) {}

    public function index(): Response
    {
        $formations = Formation::published()
            ->orderBy('date_start')
            ->get();

        return Inertia::render('Formations/Index', [
            'formations' => $formations,
        ]);
    }

    public function show(string $slug): Response
    {
        $formation = Formation::published()
            ->where('slug', $slug)
            ->firstOrFail();

        return Inertia::render('Formations/Show', [
            'formation' => $formation,
        ]);
    }

    public function register(Request $request, string $slug): RedirectResponse
    {
        $formation = Formation::published()->where('slug', $slug)->firstOrFail();

        if ($formation->isFull()) {
            return back()->with('error', 'Cette formation est complète.');
        }

        $validated = $request->validate([
            'name'  => 'required|string|max:200',
            'email' => 'required|email|max:191',
            'phone' => 'required|string|max:30',
            'notes' => 'nullable|string|max:1000',
        ]);

        $registration = FormationRegistration::create([
            ...$validated,
            'formation_id'   => $formation->id,
            'status'         => 'pending',
            'payment_status' => $formation->is_free ? 'paid' : 'unpaid',
        ]);

        // Email de confirmation au participant
        try {
            Mail::to($registration->email)
                ->send(new FormationRegistrationConfirmation($registration, $formation));
        } catch (\Exception $e) {
            Log::warning('Formation registration email failed', ['error' => $e->getMessage()]);
        }

        // Notification Telegram
        $this->telegram->notifyNewFormationRegistration($registration->load('formation'));

        // Formation gratuite → confirmation directe
        if ($formation->is_free) {
            $registration->update(['status' => 'confirmed']);

            return redirect()->route('formation.confirmation', ['token' => $registration->token]);
        }

        // Formation payante → GeniusPay
        try {
            $payment = $this->geniusPay->createPayment([
                'amount'      => (int) round($formation->price),
                'currency'    => 'XOF',
                'description' => "Inscription — {$formation->title}",
                'customer'    => [
                    'name'  => $registration->name,
                    'email' => $registration->email,
                    'phone' => $registration->phone,
                ],
                'success_url' => route('formation.payment.success', ['registration' => $registration->token]),
                'error_url'   => route('formation.payment.error',   ['registration' => $registration->token]),
                'metadata'    => [
                    'registration_token' => $registration->token,
                    'formation_id'       => $formation->id,
                    'formation_title'    => $formation->title,
                ],
            ]);

            $registration->update(['payment_ref' => $payment['reference'] ?? null]);

            $checkoutUrl = $payment['checkout_url'] ?? $payment['payment_url'] ?? null;
            if (empty($checkoutUrl)) {
                throw new \RuntimeException('Aucune URL de paiement retournée par GeniusPay');
            }

            return redirect($checkoutUrl);

        } catch (\Exception $e) {
            Log::error('GeniusPay formation error', ['error' => $e->getMessage()]);

            return redirect()->route('formation.confirmation', ['token' => $registration->token])
                ->with('warning', 'Inscription enregistrée — paiement en attente.');
        }
    }

    public function confirmation(string $token): Response
    {
        $registration = FormationRegistration::with('formation')
            ->where('token', $token)
            ->firstOrFail();

        return Inertia::render('Formations/Confirmation', [
            'registration' => $registration,
            'formation'    => $registration->formation,
        ]);
    }

    public function paymentSuccess(Request $request): Response|RedirectResponse
    {
        $token = $request->query('registration');
        if (! $token) return redirect('/formations');

        $registration = FormationRegistration::with('formation')
            ->where('token', $token)->firstOrFail();

        // Vérification GeniusPay
        if ($registration->payment_ref) {
            try {
                $payment = $this->geniusPay->getPayment($registration->payment_ref);
                if ($payment['status'] === 'completed') {
                    $registration->update(['status' => 'confirmed', 'payment_status' => 'paid']);
                }
            } catch (\Exception $e) {
                Log::warning('Formation payment verification failed', ['error' => $e->getMessage()]);
            }
        }

        return Inertia::render('Formations/Confirmation', [
            'registration' => $registration->fresh('formation'),
            'formation'    => $registration->formation,
        ]);
    }

    public function paymentError(Request $request): RedirectResponse
    {
        $token = $request->query('registration');
        if ($token) {
            FormationRegistration::where('token', $token)
                ->update(['payment_status' => 'failed']);
        }

        return redirect('/formations')->with('error', 'Paiement non abouti. Réessayez ou contactez-nous.');
    }
}
