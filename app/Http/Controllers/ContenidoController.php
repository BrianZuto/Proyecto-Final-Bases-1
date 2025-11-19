<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ContenidoController extends Controller
{
    /**
     * Muestra la lista de contenido
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Construir query base con SQL directo
        $whereConditions = [];
        $params = [];

        // Si no es administrador, solo mostrar contenido publicado
        if (!$user->isAdministrador()) {
            $whereConditions[] = "(c.publicado = 1 AND (c.fecha_publicacion IS NULL OR c.fecha_publicacion <= ?))";
            $params[] = now()->toDateString();
        }

        // Filtros
        if ($request->filled('tipo')) {
            $whereConditions[] = "c.tipo = ?";
            $params[] = $request->tipo;
        }
        if ($request->filled('categoria')) {
            $whereConditions[] = "c.categoria = ?";
            $params[] = $request->categoria;
        }
        if ($request->filled('buscar')) {
            $buscar = '%' . $request->buscar . '%';
            $whereConditions[] = "(c.titulo LIKE ? OR c.descripcion LIKE ? OR c.contenido LIKE ?)";
            $params[] = $buscar;
            $params[] = $buscar;
            $params[] = $buscar;
        }

        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

        // Paginación
        $perPage = $request->get('per_page', 12);
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        // Contar total
        $total = DB::selectOne("
            SELECT COUNT(*) as total 
            FROM contenido c
            LEFT JOIN users u ON c.autor_id = u.id
            {$whereClause}
        ", $params)->total ?? 0;

        // Obtener contenido
        $contenidoData = DB::select("
            SELECT 
                c.*,
                u.name as autor_nombre,
                u.primer_nombre,
                u.primer_apellido
            FROM contenido c
            LEFT JOIN users u ON c.autor_id = u.id
            {$whereClause}
            ORDER BY c.created_at DESC
            LIMIT ? OFFSET ?
        ", array_merge($params, [$perPage, $offset]));

        // Crear paginador manual
        $contenido = new \Illuminate\Pagination\LengthAwarePaginator(
            collect($contenidoData),
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Obtener categorías únicas para filtro
        $categorias = DB::select("
            SELECT DISTINCT categoria 
            FROM contenido 
            WHERE categoria IS NOT NULL
        ");
        $categorias = collect($categorias)->pluck('categoria')->toArray();

        // Estadísticas (solo para administradores)
        $stats = null;
        if ($user->isAdministrador()) {
            $stats = [
                'total' => DB::selectOne("SELECT COUNT(*) as total FROM contenido")->total ?? 0,
                'publicados' => DB::selectOne("SELECT COUNT(*) as total FROM contenido WHERE publicado = 1")->total ?? 0,
                'borradores' => DB::selectOne("SELECT COUNT(*) as total FROM contenido WHERE publicado = 0")->total ?? 0,
                'por_tipo' => collect(DB::select("SELECT tipo, COUNT(*) as cantidad FROM contenido GROUP BY tipo")),
            ];
        }

        return view('contenido.index', compact('contenido', 'categorias', 'stats'));
    }

    /**
     * Muestra el formulario para crear nuevo contenido
     */
    public function create()
    {
        return view('contenido.create');
    }

    /**
     * Almacena un nuevo contenido
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|in:articulo,video,infografia,recurso,consejo',
            'categoria' => 'nullable|string|max:255',
            'contenido' => 'required|string',
            'imagen_url' => 'nullable|url|max:2048',
            'video_url' => 'nullable|url|max:2048',
            'archivo_url' => 'nullable|url|max:2048',
            'tags' => 'nullable|string|max:500',
            'publicado' => 'nullable|boolean',
            'fecha_publicacion' => 'nullable|date',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $publicado = $request->has('publicado') && $request->publicado ? 1 : 0;
        $fechaPublicacion = $publicado && !$request->filled('fecha_publicacion') 
            ? now()->toDateString() 
            : ($validated['fecha_publicacion'] ?? null);

        DB::insert("
            INSERT INTO contenido (
                autor_id, titulo, tipo, categoria, contenido,
                imagen_url, video_url, archivo_url, tags, publicado,
                fecha_publicacion, vistas, likes, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $user->id,
            $validated['titulo'],
            $validated['tipo'],
            $validated['categoria'] ?? null,
            $validated['contenido'],
            $validated['imagen_url'] ?? null,
            $validated['video_url'] ?? null,
            $validated['archivo_url'] ?? null,
            $validated['tags'] ?? null,
            $publicado,
            $fechaPublicacion,
            0,
            0,
            now(),
            now()
        ]);

        $contenidoId = DB::getPdo()->lastInsertId();

        return redirect()->route('contenido.show', $contenidoId)
            ->with('success', 'Contenido creado exitosamente.');
    }

    /**
     * Muestra el detalle de un contenido
     */
    public function show($contenido)
    {
        /** @var User $user */
        $user = Auth::user();

        $contenidoData = DB::selectOne("
            SELECT 
                c.*,
                u.name as autor_nombre,
                u.primer_nombre,
                u.primer_apellido
            FROM contenido c
            LEFT JOIN users u ON c.autor_id = u.id
            WHERE c.id = ?
        ", [$contenido]);

        if (!$contenidoData) {
            abort(404, 'Contenido no encontrado');
        }

        // Si no es administrador y no está publicado, no puede verlo
        if (!$user->isAdministrador() && 
            ($contenidoData->publicado != 1 || 
            ($contenidoData->fecha_publicacion && $contenidoData->fecha_publicacion > now()->toDateString()))) {
            abort(403, 'Este contenido no está disponible');
        }

        // Incrementar vistas
        DB::update("UPDATE contenido SET vistas = vistas + 1 WHERE id = ?", [$contenido]);

        // Obtener contenido relacionado
        $contenidoRelacionado = collect(DB::select("
            SELECT * 
            FROM contenido 
            WHERE id != ? 
            AND publicado = 1 
            AND (tipo = ? OR categoria = ?)
            LIMIT 4
        ", [$contenido, $contenidoData->tipo, $contenidoData->categoria]));

        return view('contenido.show', compact('contenidoData', 'contenidoRelacionado'));
    }

    /**
     * Muestra el formulario para editar contenido
     */
    public function edit($contenido)
    {
        /** @var User $user */
        $user = Auth::user();

        $contenidoData = DB::selectOne("SELECT * FROM contenido WHERE id = ?", [$contenido]);

        if (!$contenidoData) {
            abort(404, 'Contenido no encontrado');
        }

        // Solo el autor o administrador puede editar
        if (!$user->isAdministrador() && $contenidoData->autor_id != $user->id) {
            abort(403, 'No tienes permiso para editar este contenido');
        }

        return view('contenido.edit', compact('contenidoData'));
    }

    /**
     * Actualiza un contenido
     */
    public function update(Request $request, $contenido)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|in:articulo,video,infografia,recurso,consejo',
            'categoria' => 'nullable|string|max:255',
            'contenido' => 'required|string',
            'imagen_url' => 'nullable|url|max:2048',
            'video_url' => 'nullable|url|max:2048',
            'archivo_url' => 'nullable|url|max:2048',
            'tags' => 'nullable|string|max:500',
            'publicado' => 'nullable|boolean',
            'fecha_publicacion' => 'nullable|date',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $contenidoData = DB::selectOne("SELECT * FROM contenido WHERE id = ?", [$contenido]);

        if (!$contenidoData) {
            abort(404, 'Contenido no encontrado');
        }

        // Solo el autor o administrador puede editar
        if (!$user->isAdministrador() && $contenidoData->autor_id != $user->id) {
            abort(403, 'No tienes permiso para editar este contenido');
        }

        $publicado = $request->has('publicado') && $request->publicado ? 1 : 0;
        $fechaPublicacion = $publicado && !$request->filled('fecha_publicacion')
            ? ($contenidoData->fecha_publicacion ?? now()->toDateString())
            : ($validated['fecha_publicacion'] ?? $contenidoData->fecha_publicacion);

        DB::update("
            UPDATE contenido SET 
                titulo = ?, tipo = ?, categoria = ?, contenido = ?,
                imagen_url = ?, video_url = ?, archivo_url = ?, tags = ?,
                publicado = ?, fecha_publicacion = ?, updated_at = ?
            WHERE id = ?
        ", [
            $validated['titulo'],
            $validated['tipo'],
            $validated['categoria'] ?? null,
            $validated['contenido'],
            $validated['imagen_url'] ?? null,
            $validated['video_url'] ?? null,
            $validated['archivo_url'] ?? null,
            $validated['tags'] ?? null,
            $publicado,
            $fechaPublicacion,
            now(),
            $contenido
        ]);

        return redirect()->route('contenido.show', $contenido)
            ->with('success', 'Contenido actualizado exitosamente.');
    }

    /**
     * Elimina un contenido
     */
    public function destroy($contenido)
    {
        /** @var User $user */
        $user = Auth::user();

        $contenidoData = DB::selectOne("SELECT * FROM contenido WHERE id = ?", [$contenido]);

        if (!$contenidoData) {
            abort(404, 'Contenido no encontrado');
        }

        // Solo el autor o administrador puede eliminar
        if (!$user->isAdministrador() && $contenidoData->autor_id != $user->id) {
            abort(403, 'No tienes permiso para eliminar este contenido');
        }

        DB::delete("DELETE FROM contenido WHERE id = ?", [$contenido]);

        return redirect()->route('contenido.index')
            ->with('success', 'Contenido eliminado exitosamente.');
    }

    /**
     * Incrementa los likes de un contenido
     */
    public function like($contenido)
    {
        DB::update("UPDATE contenido SET likes = likes + 1 WHERE id = ?", [$contenido]);

        $likes = DB::selectOne("SELECT likes FROM contenido WHERE id = ?", [$contenido])->likes ?? 0;

        return response()->json(['likes' => $likes]);
    }
}
