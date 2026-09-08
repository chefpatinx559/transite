import { useState, useEffect, useRef, ChangeEvent, DragEvent } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Upload, X, ArrowLeft, Save } from 'lucide-react'
import { productsApi } from '@/api/endpoints'
import { cn, storageUrl } from '@/lib/utils'
import { useToast } from '@/hooks/useToast'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Switch } from '@/components/ui/switch'
import {
  Select, SelectTrigger, SelectValue,
  SelectContent, SelectItem,
} from '@/components/ui/select'
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card'

interface Category {
  id: number
  name: string
  slug?: string
}

interface ProductData {
  id: number
  name: string
  category_id?: number | null
  description?: string
  price: number | string
  compare_price?: number | string | null
  stock: number
  type?: string
  sku?: string
  weight?: number | string | null
  is_published: boolean
  is_featured: boolean
  photo_url?: string | null
}

interface FormErrors {
  name?: string
  price?: string
  stock?: string
  category_id?: string
  description?: string
  compare_price?: string
  sku?: string
  weight?: string
  [key: string]: string | undefined
}

interface ApiErrorResponse {
  errors?: Record<string, string[]>
  message?: string
}

export default function ProductFormPage() {
  const { id } = useParams<{ id?: string }>()
  const navigate = useNavigate()
  const queryClient = useQueryClient()
  const { success, error: toastError } = useToast()
  const isEdit = !!id
  const fileInputRef = useRef<HTMLInputElement>(null)

  const [form, setForm] = useState({
    name: '',
    category_id: '',
    description: '',
    price: '',
    compare_price: '',
    stock: '0',
    type: 'physical',
    sku: '',
    weight: '',
    is_published: false,
    is_featured: false,
  })
  const [photo, setPhoto] = useState<File | null>(null)
  const [previewUrl, setPreviewUrl] = useState<string | null>(null)
  const [errors, setErrors] = useState<FormErrors>({})
  const [isDragging, setIsDragging] = useState(false)

  const { data: categoriesData } = useQuery({
    queryKey: ['categories', 'produit'],
    queryFn: () => productsApi.categories('produit').then(r => r.data as Category[] | { data?: Category[] }),
  })
  const categories: Category[] = Array.isArray(categoriesData)
    ? categoriesData
    : (categoriesData as { data?: Category[] })?.data ?? []

  const { data: productData } = useQuery({
    queryKey: ['product', id],
    queryFn: () => productsApi.get(Number(id)).then(r => (r.data as { data?: ProductData } | ProductData)),
    enabled: isEdit,
  })

  useEffect(() => {
    const product = (productData as { data?: ProductData })?.data ?? (productData as ProductData | undefined)
    if (!product) return
    setForm({
      name: product.name ?? '',
      category_id: product.category_id != null ? String(product.category_id) : '',
      description: product.description ?? '',
      price: String(product.price ?? ''),
      compare_price: product.compare_price != null ? String(product.compare_price) : '',
      stock: String(product.stock ?? 0),
      type: product.type ?? 'physical',
      sku: product.sku ?? '',
      weight: product.weight != null ? String(product.weight) : '',
      is_published: product.is_published ?? false,
      is_featured: product.is_featured ?? false,
    })
    const imgs = (product as unknown as { images?: Array<{url:string}|string> }).images
    if (imgs && imgs.length > 0) {
      const first = imgs[0]
      const rawUrl = typeof first === 'string' ? first : first.url
      setPreviewUrl(storageUrl(rawUrl))
    } else if (product.photo_url) {
      setPreviewUrl(storageUrl(product.photo_url))
    }
  }, [productData])

  function handleFile(file: File) {
    setPhoto(file)
    setPreviewUrl(URL.createObjectURL(file))
  }

  function onFileChange(e: ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0]
    if (file) handleFile(file)
  }

  function onDrop(e: DragEvent<HTMLDivElement>) {
    e.preventDefault()
    setIsDragging(false)
    const file = e.dataTransfer.files?.[0]
    if (file && file.type.startsWith('image/')) handleFile(file)
  }

  function removePhoto() {
    setPhoto(null)
    setPreviewUrl(null)
    if (fileInputRef.current) fileInputRef.current.value = ''
  }

  const mutation = useMutation({
    mutationFn: () => {
      const fd = new FormData()
      // Champs optionnels vides → ne pas envoyer (évite erreurs de validation)
      const optionalFields = ['compare_price', 'sku', 'weight', 'category_id']
      Object.entries(form).forEach(([k, v]) => {
        if (optionalFields.includes(k) && (v === '' || v === null)) return
        if (typeof v === 'boolean') fd.append(k, v ? '1' : '0')
        else fd.append(k, String(v))
      })
      if (photo) fd.append('photo', photo)
      if (isEdit) {
        return productsApi.update(Number(id), fd)
      }
      return productsApi.create(fd)
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['products'] })
      success(isEdit ? 'Produit mis à jour avec succès.' : 'Produit créé avec succès.')
      navigate('/produits')
    },
    onError: (err: unknown) => {
      const axiosErr = err as { response?: { data?: ApiErrorResponse } }
      const apiErrors = axiosErr.response?.data?.errors
      if (apiErrors) {
        const mapped: FormErrors = {}
        Object.entries(apiErrors).forEach(([k, msgs]) => {
          mapped[k] = Array.isArray(msgs) ? msgs[0] : String(msgs)
        })
        setErrors(mapped)
        toastError('Veuillez corriger les erreurs dans le formulaire.')
      } else {
        toastError('Une erreur est survenue. Veuillez réessayer.')
      }
    },
  })

  function set(field: string, value: string | boolean) {
    setForm(prev => ({ ...prev, [field]: value }))
    if (errors[field]) setErrors(prev => ({ ...prev, [field]: undefined }))
  }

  return (
    <div className="max-w-3xl mx-auto space-y-6">
      <div className="flex items-center gap-3">
        <Button variant="outline" size="sm" onClick={() => navigate('/produits')} aria-label="Retour à la liste des produits">
          <ArrowLeft size={15} />
          Retour
        </Button>
        <div>
          <h1 className="font-sans font-bold text-2xl text-[#0D0D0D]">
            {isEdit ? 'Modifier le produit' : 'Nouveau produit'}
          </h1>
        </div>
      </div>

      {/* Photo */}
      <Card>
        <CardHeader>
          <CardTitle>Photo du produit</CardTitle>
        </CardHeader>
        <CardContent>
          {previewUrl ? (
            <div className="relative inline-block">
              <img src={previewUrl} alt="Aperçu" className="w-40 h-40 object-cover rounded-[12px] border border-[#E5E7EB]" />
              <button
                onClick={removePhoto}
                aria-label="Supprimer la photo"
                className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors"
              >
                <X size={12} />
              </button>
            </div>
          ) : (
            <div
              onDragOver={e => { e.preventDefault(); setIsDragging(true) }}
              onDragLeave={() => setIsDragging(false)}
              onDrop={onDrop}
              onClick={() => fileInputRef.current?.click()}
              aria-label="Zone de dépôt de photo"
              className={cn(
                'border-2 border-dashed rounded-[12px] p-10 text-center cursor-pointer transition-colors',
                isDragging ? 'border-[#F4620A] bg-orange-50' : 'border-[#E5E7EB] hover:border-[#F4620A]/50',
              )}
            >
              <Upload size={28} className="mx-auto mb-3 text-gray-400" />
              <p className="text-sm text-gray-500">Glissez une image ici ou cliquez pour choisir</p>
              <p className="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — max 5 Mo</p>
            </div>
          )}
          <input
            ref={fileInputRef}
            type="file"
            accept="image/*"
            onChange={onFileChange}
            className="hidden"
            aria-label="Sélectionner une photo"
          />
        </CardContent>
      </Card>

      {/* Informations générales */}
      <Card>
        <CardHeader>
          <CardTitle>Informations générales</CardTitle>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="space-y-1.5">
            <Label htmlFor="name">Nom du produit *</Label>
            <Input
              id="name"
              value={form.name}
              onChange={e => set('name', e.target.value)}
              className={cn(errors.name && 'border-red-400')}
              placeholder="Ex. : Dalle de béton 60x60"
            />
            {errors.name && <p className="text-xs text-red-600" role="alert">{errors.name}</p>}
          </div>

          <div className="grid grid-cols-2 gap-4">
            {/* Catégorie */}
            <div className="space-y-1.5">
              <Label htmlFor="category_id">Catégorie</Label>
              <Select value={form.category_id} onValueChange={v => set('category_id', v)}>
                <SelectTrigger id="category_id">
                  <SelectValue placeholder="— Sélectionner —" />
                </SelectTrigger>
                <SelectContent>
                  {categories.map(c => (
                    <SelectItem
                      key={c.id}
                      value={String(c.id)}
                      disabled={c.slug === 'formation'}
                    >
                      {c.name}{c.slug === 'formation' && ' (bientôt)'}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              {errors.category_id && <p className="text-xs text-red-600" role="alert">{errors.category_id}</p>}
            </div>

            {/* Type */}
            <div className="space-y-1.5">
              <Label htmlFor="type">Type</Label>
              <Select value={form.type} onValueChange={v => set('type', v)}>
                <SelectTrigger id="type">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="physical">Physique</SelectItem>
                  <SelectItem value="digital">Numérique</SelectItem>
                  <SelectItem value="course">Formation</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>

          {/* Description */}
          <div className="space-y-1.5">
            <Label htmlFor="description">Description</Label>
            <Textarea
              id="description"
              rows={4}
              value={form.description}
              onChange={e => set('description', e.target.value)}
              placeholder="Décrivez le produit…"
            />
            {errors.description && <p className="text-xs text-red-600" role="alert">{errors.description}</p>}
          </div>
        </CardContent>
      </Card>

      {/* Prix & stock */}
      <Card>
        <CardHeader>
          <CardTitle>Prix &amp; stock</CardTitle>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <div className="space-y-1.5">
              <Label htmlFor="price">Prix (FCFA) *</Label>
              <Input
                id="price"
                type="number"
                min="0"
                value={form.price}
                onChange={e => set('price', e.target.value)}
                className={cn(errors.price && 'border-red-400')}
                placeholder="0"
              />
              {errors.price && <p className="text-xs text-red-600" role="alert">{errors.price}</p>}
            </div>
            <div className="space-y-1.5">
              <Label htmlFor="compare_price">Prix barré (FCFA)</Label>
              <Input
                id="compare_price"
                type="number"
                min="0"
                value={form.compare_price}
                onChange={e => set('compare_price', e.target.value)}
                className={cn(errors.compare_price && 'border-red-400')}
                placeholder="0"
              />
              {errors.compare_price && <p className="text-xs text-red-600" role="alert">{errors.compare_price}</p>}
            </div>
          </div>

          <div className="grid grid-cols-2 gap-4">
            <div className="space-y-1.5">
              <Label htmlFor="stock">Stock *</Label>
              <Input
                id="stock"
                type="number"
                min="0"
                value={form.stock}
                onChange={e => set('stock', e.target.value)}
                className={cn(errors.stock && 'border-red-400')}
                placeholder="0"
              />
              {errors.stock && <p className="text-xs text-red-600" role="alert">{errors.stock}</p>}
            </div>
            <div className="space-y-1.5">
              <Label htmlFor="sku">SKU</Label>
              <Input
                id="sku"
                value={form.sku}
                onChange={e => set('sku', e.target.value)}
                className={cn(errors.sku && 'border-red-400')}
                placeholder="Ex. : NS-DALLE-6060"
              />
              {errors.sku && <p className="text-xs text-red-600" role="alert">{errors.sku}</p>}
            </div>
          </div>

          <div className="space-y-1.5">
            <Label htmlFor="weight">Poids (kg)</Label>
            <Input
              id="weight"
              type="number"
              min="0"
              step="0.01"
              value={form.weight}
              onChange={e => set('weight', e.target.value)}
              className={cn(errors.weight && 'border-red-400')}
              placeholder="0.00"
            />
            {errors.weight && <p className="text-xs text-red-600" role="alert">{errors.weight}</p>}
          </div>
        </CardContent>
      </Card>

      {/* Options de publication */}
      <Card>
        <CardHeader>
          <CardTitle>Options de publication</CardTitle>
        </CardHeader>
        <CardContent className="space-y-3">
          {[
            { key: 'is_published', label: 'Produit publié (visible sur la boutique)' },
            { key: 'is_featured',  label: 'Produit mis en avant' },
          ].map(({ key, label }) => (
            <div key={key} className="flex items-center gap-3">
              <Switch
                id={key}
                checked={form[key as keyof typeof form] as boolean}
                onCheckedChange={v => set(key, v)}
              />
              <Label htmlFor={key} className="cursor-pointer">{label}</Label>
            </div>
          ))}
        </CardContent>
      </Card>

      <div className="flex justify-end gap-3 pb-6">
        <Button variant="outline" onClick={() => navigate('/produits')} disabled={mutation.isPending}>
          Annuler
        </Button>
        <Button
          onClick={() => mutation.mutate()}
          loading={mutation.isPending}
          aria-label={isEdit ? 'Enregistrer les modifications' : 'Créer le produit'}
        >
          <Save className="w-4 h-4" aria-hidden="true" />
          {isEdit ? 'Enregistrer les modifications' : 'Créer le produit'}
        </Button>
      </div>
    </div>
  )
}
