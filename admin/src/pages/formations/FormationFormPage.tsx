import { useState, useEffect, useRef } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { ArrowLeft, ImagePlus, X } from 'lucide-react'
import { formationsApi } from '@/api/endpoints'
import { storageUrl } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Card, CardHeader, CardContent } from '@/components/ui/card'
import { Switch } from '@/components/ui/switch'
import { useToast } from '@/hooks/useToast'

interface FormErrors { [key: string]: string | undefined }

const emptyForm = {
  title: '', excerpt: '', description: '',
  type: 'online', is_free: '1', price: '',
  duration: '', instructor_name: '',
  date_start: '', date_end: '',
  location: '', platform_link: '',
  max_participants: '', is_published: '0',
}

export default function FormationFormPage() {
  const { id } = useParams<{ id?: string }>()
  const navigate = useNavigate()
  const queryClient = useQueryClient()
  const { success, error } = useToast()
  const isEdit = !!id
  const fileInputRef = useRef<HTMLInputElement>(null)

  const [form, setForm]                   = useState(emptyForm)
  const [errors, setErrors]               = useState<FormErrors>({})
  const [coverFile, setCoverFile]         = useState<File | null>(null)
  const [coverPreview, setCoverPreview]   = useState<string | null>(null)
  const [existingCover, setExistingCover] = useState<string | null>(null)
  const [removeCover, setRemoveCover]     = useState(false)
  const [isDragging, setIsDragging]       = useState(false)

  // Ref pour éviter la closure stale dans useMutation
  const coverFileRef   = useRef<File | null>(null)
  const removeCoverRef = useRef(false)
  useEffect(() => { coverFileRef.current   = coverFile   }, [coverFile])
  useEffect(() => { removeCoverRef.current = removeCover }, [removeCover])

  const { data: formationData } = useQuery({
    queryKey: ['formation', id],
    queryFn: () => formationsApi.get(Number(id)).then(r => r.data),
    enabled: isEdit,
  })

  useEffect(() => {
    const f = formationData?.data ?? formationData
    if (!f) return
    setForm({
      title:            f.title ?? '',
      excerpt:          f.excerpt ?? '',
      description:      f.description ?? '',
      type:             f.type ?? 'online',
      is_free:          f.is_free ? '1' : '0',
      price:            f.price != null ? String(f.price) : '',
      duration:         f.duration ?? '',
      instructor_name:  f.instructor_name ?? '',
      date_start:       f.date_start ? f.date_start.slice(0, 16) : '',
      date_end:         f.date_end ? f.date_end.slice(0, 16) : '',
      location:         f.location ?? '',
      platform_link:    f.platform_link ?? '',
      max_participants: f.max_participants != null ? String(f.max_participants) : '',
      is_published:     f.is_published ? '1' : '0',
    })
    if (f.cover_image) setExistingCover(f.cover_image)
  }, [formationData])

  function applyFile(file: File | null) {
    if (!file) return
    setCoverFile(file); setRemoveCover(false); setExistingCover(null)
    const reader = new FileReader()
    reader.onload = e => setCoverPreview(e.target?.result as string)
    reader.readAsDataURL(file)
  }

  function clearCover() {
    setCoverFile(null); setCoverPreview(null); setRemoveCover(true); setExistingCover(null)
    if (fileInputRef.current) fileInputRef.current.value = ''
  }

  function onDrop(e: React.DragEvent) {
    e.preventDefault(); setIsDragging(false)
    const file = e.dataTransfer.files[0]
    if (file?.type.startsWith('image/')) applyFile(file)
  }

  const mutation = useMutation({
    mutationFn: () => {
      const fd = new FormData()
      Object.entries(form).forEach(([k, v]) => { if (v !== '') fd.append(k, v) })
      // Utilise les refs pour éviter les closures stales
      if (coverFileRef.current)   fd.append('cover_image', coverFileRef.current)
      if (removeCoverRef.current) fd.append('remove_cover', '1')
      return isEdit ? formationsApi.update(Number(id), fd) : formationsApi.create(fd)
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['formations'] })
      success(isEdit ? 'Formation mise à jour.' : 'Formation créée.')
      navigate('/formations')
    },
    onError: (err: unknown) => {
      const apiErrors = (err as { response?: { data?: { errors?: Record<string, string[]> } } })?.response?.data?.errors
      if (apiErrors) {
        const mapped: FormErrors = {}
        Object.entries(apiErrors).forEach(([k, msgs]) => { mapped[k] = Array.isArray(msgs) ? msgs[0] : String(msgs) })
        setErrors(mapped)
      } else {
        error(isEdit ? 'Impossible de mettre à jour.' : 'Impossible de créer la formation.')
      }
    },
  })

  function set(field: string, value: string) {
    setForm(prev => ({ ...prev, [field]: value }))
    if (errors[field]) setErrors(prev => ({ ...prev, [field]: undefined }))
  }

  const currentImage = coverPreview ?? (existingCover ? storageUrl(existingCover) : null)
  const isFree = form.is_free === '1'

  return (
    <div className="max-w-3xl mx-auto space-y-6">
      <div className="flex items-center gap-3">
        <Button variant="outline" size="sm" onClick={() => navigate('/formations')}>
          <ArrowLeft size={15} /> Retour
        </Button>
        <h1 className="font-sans font-bold text-2xl text-[#0D0D0D] dark:text-white">
          {isEdit ? 'Modifier la formation' : 'Nouvelle formation'}
        </h1>
      </div>

      {/* Informations générales */}
      <Card>
        <CardHeader title="Informations" />
        <CardContent className="space-y-4">
          <div className="space-y-1.5">
            <Label htmlFor="title">Titre *</Label>
            <Input id="title" value={form.title} onChange={e => set('title', e.target.value)} placeholder="Ex. : Importer depuis la Chine" />
            {errors.title && <p className="text-xs text-red-600">{errors.title}</p>}
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div className="space-y-1.5">
              <Label>Type *</Label>
              <Select value={form.type} onValueChange={v => set('type', v)}>
                <SelectTrigger><SelectValue /></SelectTrigger>
                <SelectContent>
                  <SelectItem value="online">En ligne</SelectItem>
                  <SelectItem value="presentielle">Présentielle</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-1.5">
              <Label htmlFor="duration">Durée</Label>
              <Input id="duration" value={form.duration} onChange={e => set('duration', e.target.value)} placeholder="Ex. : 2 jours, 4 heures" />
            </div>
          </div>
          <div className="space-y-1.5">
            <Label htmlFor="instructor_name">Formateur</Label>
            <Input id="instructor_name" value={form.instructor_name} onChange={e => set('instructor_name', e.target.value)} placeholder="Nom du formateur" />
          </div>
          <div className="space-y-1.5">
            <Label htmlFor="excerpt">Résumé court</Label>
            <Textarea id="excerpt" rows={2} value={form.excerpt} onChange={e => set('excerpt', e.target.value)} placeholder="Description courte…" />
          </div>
          <div className="space-y-1.5">
            <Label htmlFor="description">Description complète (HTML)</Label>
            <Textarea id="description" rows={8} value={form.description} onChange={e => set('description', e.target.value)} placeholder="Programme, objectifs, prérequis…" className="font-mono text-xs" />
          </div>
        </CardContent>
      </Card>

      {/* Tarif */}
      <Card>
        <CardHeader title="Tarif" />
        <CardContent className="space-y-4">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-[#0D0D0D] dark:text-white">Formation gratuite</p>
              <p className="text-xs text-gray-500">Désactivez pour définir un prix</p>
            </div>
            <Switch checked={isFree} onCheckedChange={v => set('is_free', v ? '1' : '0')} />
          </div>
          {!isFree && (
            <div className="space-y-1.5">
              <Label htmlFor="price">Prix (FCFA) *</Label>
              <Input id="price" type="number" min="0" value={form.price} onChange={e => set('price', e.target.value)} placeholder="Ex. : 25000" />
              {errors.price && <p className="text-xs text-red-600">{errors.price}</p>}
            </div>
          )}
          <div className="space-y-1.5">
            <Label htmlFor="max_participants">Participants max (vide = illimité)</Label>
            <Input id="max_participants" type="number" min="1" value={form.max_participants} onChange={e => set('max_participants', e.target.value)} placeholder="Ex. : 30" />
          </div>
        </CardContent>
      </Card>

      {/* Dates & Lieu */}
      <Card>
        <CardHeader title="Dates & Accès" />
        <CardContent className="space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <div className="space-y-1.5">
              <Label htmlFor="date_start">Date de début</Label>
              <input id="date_start" type="datetime-local" value={form.date_start}
                onChange={e => set('date_start', e.target.value)}
                className="w-full border border-[#E5E7EB] dark:border-white/10 dark:bg-[#1e1e1e] dark:text-white rounded-[10px] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A]" />
            </div>
            <div className="space-y-1.5">
              <Label htmlFor="date_end">Date de fin</Label>
              <input id="date_end" type="datetime-local" value={form.date_end}
                onChange={e => set('date_end', e.target.value)}
                className="w-full border border-[#E5E7EB] dark:border-white/10 dark:bg-[#1e1e1e] dark:text-white rounded-[10px] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A]" />
            </div>
          </div>
          {form.type === 'presentielle' && (
            <div className="space-y-1.5">
              <Label htmlFor="location">Lieu</Label>
              <Input id="location" value={form.location} onChange={e => set('location', e.target.value)} placeholder="Ex. : Cocody, Abidjan" />
            </div>
          )}
          {form.type === 'online' && (
            <div className="space-y-1.5">
              <Label htmlFor="platform_link">Lien plateforme (Zoom, Meet…)</Label>
              <Input id="platform_link" type="url" value={form.platform_link} onChange={e => set('platform_link', e.target.value)} placeholder="https://zoom.us/j/..." />
              {errors.platform_link && <p className="text-xs text-red-600">{errors.platform_link}</p>}
            </div>
          )}
        </CardContent>
      </Card>

      {/* Photo */}
      <Card>
        <CardHeader title="Photo de couverture" />
        <CardContent>
          {/* Input caché — associé via htmlFor pour un déclenchement fiable */}
          <input
            ref={fileInputRef}
            id="formation-cover-input"
            type="file"
            accept="image/*"
            className="hidden"
            onChange={e => applyFile(e.target.files?.[0] ?? null)}
          />

          {currentImage ? (
            <div className="relative group rounded-xl overflow-hidden border border-[#E5E7EB] dark:border-white/10">
              <img src={currentImage} alt="Couverture" className="w-full h-48 object-cover" />
              <div className="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all flex items-center justify-center gap-3 opacity-0 group-hover:opacity-100">
                <label
                  htmlFor="formation-cover-input"
                  className="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-white/90 text-[#0D0D0D] rounded-lg cursor-pointer hover:bg-white transition-colors"
                >
                  <ImagePlus className="w-3.5 h-3.5" /> Changer
                </label>
                <Button type="button" variant="destructive" size="sm" onClick={clearCover}>
                  <X className="w-4 h-4" /> Supprimer
                </Button>
              </div>
            </div>
          ) : (
            <label
              htmlFor="formation-cover-input"
              onDragOver={e => { e.preventDefault(); setIsDragging(true) }}
              onDragLeave={() => setIsDragging(false)}
              onDrop={onDrop}
              className={`w-full h-40 rounded-xl border-2 border-dashed flex flex-col items-center justify-center gap-3 cursor-pointer transition-all ${isDragging ? 'border-[#F4620A] bg-[#F4620A]/5' : 'border-[#E5E7EB] dark:border-white/15 hover:border-[#F4620A]/60'}`}
            >
              <div className="w-12 h-12 rounded-full bg-[#F4620A]/10 flex items-center justify-center">
                <ImagePlus className="w-5 h-5 text-[#F4620A]" />
              </div>
              <div className="text-center">
                <p className="text-sm font-medium text-[#0D0D0D] dark:text-white">Cliquez ou déposez une image</p>
                <p className="text-xs text-gray-400 mt-0.5">PNG, JPG, WebP — 3 Mo max</p>
              </div>
            </label>
          )}
        </CardContent>
      </Card>

      {/* Publication */}
      <Card>
        <CardHeader title="Publication" />
        <CardContent>
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-[#0D0D0D] dark:text-white">Publier la formation</p>
              <p className="text-xs text-gray-500">Visible sur le site public</p>
            </div>
            <Switch checked={form.is_published === '1'} onCheckedChange={v => set('is_published', v ? '1' : '0')} />
          </div>
        </CardContent>
      </Card>

      <div className="flex justify-end gap-3 pb-6">
        <Button variant="outline" onClick={() => navigate('/formations')} disabled={mutation.isPending}>Annuler</Button>
        <Button onClick={() => mutation.mutate()} loading={mutation.isPending}>
          {isEdit ? 'Enregistrer' : 'Créer la formation'}
        </Button>
      </div>
    </div>
  )
}
