import { useState, useEffect, useRef } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { ArrowLeft, ImagePlus, X } from 'lucide-react'
import { postsApi } from '@/api/endpoints'
import { storageUrl } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Card, CardHeader, CardContent } from '@/components/ui/card'
import { useToast } from '@/hooks/useToast'

interface Category { id: number; name: string; type?: string }

interface PostData {
  id: number
  title: string
  category_id?: number | null
  excerpt?: string
  content?: string
  reading_time?: number | null
  status: string
  published_at?: string | null
  cover_image?: string | null
}

interface FormErrors { [key: string]: string | undefined }

export default function PostFormPage() {
  const { id } = useParams<{ id?: string }>()
  const navigate = useNavigate()
  const queryClient = useQueryClient()
  const { success, error } = useToast()
  const isEdit = !!id
  const fileInputRef = useRef<HTMLInputElement>(null)

  const [form, setForm] = useState({
    title: '',
    category_id: '',
    excerpt: '',
    content: '',
    reading_time: '',
    status: 'draft',
    published_at: '',
  })
  const [errors, setErrors]         = useState<FormErrors>({})
  const [coverFile, setCoverFile]   = useState<File | null>(null)
  const [coverPreview, setCoverPreview] = useState<string | null>(null)
  const [existingCover, setExistingCover] = useState<string | null>(null)
  const [removeCover, setRemoveCover]     = useState(false)
  const [isDragging, setIsDragging]       = useState(false)

  // ── Catégories ──
  const { data: categoriesData } = useQuery({
    queryKey: ['categories', 'blog'],
    queryFn: () => postsApi.categories('blog').then(r => r.data as Category[] | { data?: Category[] }),
  })
  const categories: Category[] = Array.isArray(categoriesData)
    ? categoriesData
    : (categoriesData as { data?: Category[] })?.data ?? []

  // ── Chargement article ──
  const { data: postData } = useQuery({
    queryKey: ['post', id],
    queryFn: () => postsApi.get(Number(id)).then(r => r.data as PostData | { data?: PostData }),
    enabled: isEdit,
  })

  useEffect(() => {
    const post = (postData as { data?: PostData })?.data ?? (postData as PostData | undefined)
    if (!post) return
    setForm({
      title: post.title ?? '',
      category_id: post.category_id != null ? String(post.category_id) : '',
      excerpt: post.excerpt ?? '',
      content: post.content ?? '',
      reading_time: post.reading_time != null ? String(post.reading_time) : '',
      status: post.status ?? 'draft',
      published_at: post.published_at ? post.published_at.slice(0, 16) : '',
    })
    if (post.cover_image) setExistingCover(post.cover_image)
  }, [postData])

  // ── Helpers image ──
  function applyFile(file: File | null) {
    if (!file) return
    setCoverFile(file)
    setRemoveCover(false)
    setExistingCover(null)
    const reader = new FileReader()
    reader.onload = e => setCoverPreview(e.target?.result as string)
    reader.readAsDataURL(file)
  }

  function clearCover() {
    setCoverFile(null)
    setCoverPreview(null)
    setRemoveCover(true)
    setExistingCover(null)
    if (fileInputRef.current) fileInputRef.current.value = ''
  }

  function onDrop(e: React.DragEvent) {
    e.preventDefault()
    setIsDragging(false)
    const file = e.dataTransfer.files[0]
    if (file && file.type.startsWith('image/')) applyFile(file)
  }

  // ── Mutation ──
  const mutation = useMutation({
    mutationFn: () => {
      const fd = new FormData()
      fd.append('title',   form.title)
      fd.append('excerpt', form.excerpt)
      fd.append('content', form.content)
      fd.append('status',  form.status)
      if (form.category_id) fd.append('category_id', form.category_id)
      if (form.reading_time) fd.append('reading_time', form.reading_time)
      if (form.published_at && form.status === 'published') fd.append('published_at', form.published_at)
      if (coverFile)       fd.append('cover_image', coverFile)
      if (removeCover)     fd.append('remove_cover', '1')
      return isEdit ? postsApi.update(Number(id), fd) : postsApi.create(fd)
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['posts'] })
      success(isEdit ? 'Article mis à jour.' : 'Article créé avec succès.')
      navigate('/articles')
    },
    onError: (err: unknown) => {
      const apiErrors = (err as { response?: { data?: { errors?: Record<string, string[]> } } })?.response?.data?.errors
      if (apiErrors) {
        const mapped: FormErrors = {}
        Object.entries(apiErrors).forEach(([k, msgs]) => { mapped[k] = Array.isArray(msgs) ? msgs[0] : String(msgs) })
        setErrors(mapped)
      } else {
        error(isEdit ? "Impossible de mettre à jour l'article." : "Impossible de créer l'article.")
      }
    },
  })

  function set(field: string, value: string) {
    setForm(prev => ({ ...prev, [field]: value }))
    if (errors[field]) setErrors(prev => ({ ...prev, [field]: undefined }))
  }

  const currentImage = coverPreview ?? (existingCover ? storageUrl(existingCover) : null)

  return (
    <div className="max-w-3xl mx-auto space-y-6">
      <div className="flex items-center gap-3">
        <Button variant="outline" size="sm" onClick={() => navigate('/articles')}>
          <ArrowLeft size={15} />
          Retour
        </Button>
        <h1 className="font-sans font-bold text-2xl text-[#0D0D0D] dark:text-white">
          {isEdit ? "Modifier l'article" : 'Nouvel article'}
        </h1>
      </div>

      {/* Informations */}
      <Card>
        <CardHeader title="Informations" />
        <CardContent className="space-y-4">
          <div className="space-y-1.5">
            <Label htmlFor="title">Titre *</Label>
            <Input id="title" value={form.title} onChange={e => set('title', e.target.value)} placeholder="Titre de l'article" />
            {errors.title && <p className="text-xs text-red-600">{errors.title}</p>}
          </div>

          <div className="space-y-1.5">
            <Label htmlFor="category_id">Catégorie</Label>
            <Select value={form.category_id || '__none__'} onValueChange={v => set('category_id', v === '__none__' ? '' : v)}>
              <SelectTrigger id="category_id"><SelectValue placeholder="— Sélectionner —" /></SelectTrigger>
              <SelectContent>
                <SelectItem value="__none__">— Sélectionner —</SelectItem>
                {categories.map(c => <SelectItem key={c.id} value={String(c.id)}>{c.name}</SelectItem>)}
              </SelectContent>
            </Select>
          </div>

          <div className="space-y-1.5">
            <Label htmlFor="excerpt">Extrait / Résumé</Label>
            <Textarea id="excerpt" rows={3} value={form.excerpt} onChange={e => set('excerpt', e.target.value)} placeholder="Bref résumé de l'article…" />
          </div>

          <div className="space-y-1.5">
            <Label htmlFor="reading_time">Temps de lecture (min)</Label>
            <Input id="reading_time" type="number" min="1" value={form.reading_time} onChange={e => set('reading_time', e.target.value)} placeholder="Ex. : 5" />
          </div>
        </CardContent>
      </Card>

      {/* Photo de couverture */}
      <Card>
        <CardHeader title="Photo de couverture" />
        <CardContent>
          <input
            ref={fileInputRef}
            type="file"
            accept="image/*"
            className="hidden"
            onChange={e => applyFile(e.target.files?.[0] ?? null)}
          />

          {currentImage ? (
            /* Preview */
            <div className="relative group rounded-[12px] overflow-hidden border border-[#E5E7EB] dark:border-white/10">
              <img
                src={currentImage}
                alt="Couverture"
                className="w-full h-52 object-cover"
              />
              <div className="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all flex items-center justify-center gap-3 opacity-0 group-hover:opacity-100">
                <Button
                  type="button"
                  variant="secondary"
                  size="sm"
                  onClick={() => fileInputRef.current?.click()}
                >
                  <ImagePlus className="w-4 h-4" />
                  Changer
                </Button>
                <Button
                  type="button"
                  variant="destructive"
                  size="sm"
                  onClick={clearCover}
                >
                  <X className="w-4 h-4" />
                  Supprimer
                </Button>
              </div>
            </div>
          ) : (
            /* Zone drag & drop */
            <button
              type="button"
              onClick={() => fileInputRef.current?.click()}
              onDragOver={e => { e.preventDefault(); setIsDragging(true) }}
              onDragLeave={() => setIsDragging(false)}
              onDrop={onDrop}
              className={`w-full h-44 rounded-[12px] border-2 border-dashed flex flex-col items-center justify-center gap-3 transition-all cursor-pointer
                ${isDragging
                  ? 'border-[#F4620A] bg-[#F4620A]/5'
                  : 'border-[#E5E7EB] dark:border-white/15 hover:border-[#F4620A]/60 hover:bg-[#F4620A]/3'
                }`}
            >
              <div className="w-12 h-12 rounded-full bg-[#F4620A]/10 flex items-center justify-center">
                <ImagePlus className="w-5 h-5 text-[#F4620A]" />
              </div>
              <div className="text-center">
                <p className="text-sm font-medium text-[#0D0D0D] dark:text-white">
                  {isDragging ? 'Déposez l\'image ici' : 'Cliquez ou déposez une image'}
                </p>
                <p className="text-xs text-gray-400 mt-0.5">PNG, JPG, WebP — 3 Mo max</p>
              </div>
            </button>
          )}
          {errors.cover_image && <p className="text-xs text-red-600 mt-2">{errors.cover_image}</p>}
        </CardContent>
      </Card>

      {/* Contenu */}
      <Card>
        <CardHeader title="Contenu" />
        <CardContent className="space-y-2">
          <Label htmlFor="content">Corps de l'article (HTML)</Label>
          <Textarea
            id="content"
            rows={12}
            value={form.content}
            onChange={e => set('content', e.target.value)}
            placeholder="Rédigez votre article ici…"
            className="font-mono text-xs"
          />
          {errors.content && <p className="text-xs text-red-600">{errors.content}</p>}
        </CardContent>
      </Card>

      {/* Publication */}
      <Card>
        <CardHeader title="Publication" />
        <CardContent className="space-y-4">
          <div className="space-y-1.5">
            <Label htmlFor="status">Statut *</Label>
            <Select value={form.status} onValueChange={v => set('status', v)}>
              <SelectTrigger id="status"><SelectValue /></SelectTrigger>
              <SelectContent>
                <SelectItem value="draft">Brouillon</SelectItem>
                <SelectItem value="published">Publié</SelectItem>
                <SelectItem value="archived">Archivé</SelectItem>
              </SelectContent>
            </Select>
          </div>

          {form.status === 'published' && (
            <div className="space-y-1.5">
              <Label htmlFor="published_at">Date de publication</Label>
              <input
                id="published_at"
                type="datetime-local"
                value={form.published_at}
                onChange={e => set('published_at', e.target.value)}
                className="w-full border border-[#E5E7EB] dark:border-white/10 dark:bg-[#1e1e1e] dark:text-white rounded-[10px] px-4 py-2.5 text-sm text-[#0D0D0D] focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent"
              />
            </div>
          )}
        </CardContent>
      </Card>

      <div className="flex justify-end gap-3 pb-6">
        <Button variant="outline" onClick={() => navigate('/articles')} disabled={mutation.isPending}>
          Annuler
        </Button>
        <Button onClick={() => mutation.mutate()} loading={mutation.isPending}>
          {isEdit ? 'Enregistrer' : "Créer l'article"}
        </Button>
      </div>
    </div>
  )
}
