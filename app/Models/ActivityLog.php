<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * Koneksi database khusus untuk model ini.
     * Menggunakan koneksi database kedua: mysql_logs.
     *
     * @var string
     */
    protected $connection = 'mysql_logs';

    /**
     * Nama tabel di database ecommerce_logs.
     *
     * @var string
     */
    protected $table = 'activity_logs';

    /**
     * Kolom yang dapat diisi secara mass-assignment (Insert data).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_id',
        'admin_name',
        'activity_type',
        'subject_type',
        'subject_id',
        'description',
        'object_info',
        'old_values',
        'new_values',
    ];

    /**
     * Casting tipe data kolom.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Method static helper untuk mencatat aktivitas baru (Insert data).
     * Dilengkapi error handling agar kegagalan logs tidak mengacaukan proses utama.
     *
     * @param int|null $adminId
     * @param string|null $adminName
     * @param string $activityType
     * @param string $description
     * @param string|null $subjectType
     * @param int|null $subjectId
     * @param mixed $oldValues
     * @param mixed $newValues
     * @param string|null $objectInfo
     * @return static|null
     */
    public static function record(
        ?int $adminId,
        ?string $adminName,
        string $activityType,
        string $description,
        ?string $subjectType = null,
        ?int $subjectId = null,
        $oldValues = null,
        $newValues = null,
        ?string $objectInfo = null
    ): ?self {
        try {
            $formattedOld = is_array($oldValues) 
                ? json_encode($oldValues, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) 
                : $oldValues;

            $formattedNew = is_array($newValues) 
                ? json_encode($newValues, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) 
                : $newValues;

            $info = $objectInfo ?? ($subjectType && $subjectId ? "{$subjectType} #{$subjectId}" : null);

            return static::create([
                'admin_id'      => $adminId,
                'admin_name'    => $adminName,
                'activity_type' => $activityType,
                'subject_type'  => $subjectType,
                'subject_id'    => $subjectId,
                'description'   => $description,
                'object_info'   => $info,
                'old_values'    => $formattedOld,
                'new_values'    => $formattedNew,
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal mencatat log aktivitas ke ecommerce_logs: ' . $e->getMessage(), [
                'exception'     => $e,
                'admin_id'      => $adminId,
                'activity_type' => $activityType,
                'subject_type'  => $subjectType,
                'subject_id'    => $subjectId,
            ]);
            return null;
        }
    }

    /**
     * Scope untuk memfilter aktivitas berdasarkan ID admin.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $adminId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByAdmin($query, int $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    /**
     * Scope untuk memfilter aktivitas berdasarkan jenis aktivitas.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $activityType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, string $activityType)
    {
        return $query->where('activity_type', $activityType);
    }

    /**
     * Scope untuk memfilter aktivitas berdasarkan tipe dan ID subjek/objek.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $subjectType
     * @param int|null $subjectId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBySubject($query, string $subjectType, ?int $subjectId = null)
    {
        $query->where('subject_type', $subjectType);

        if ($subjectId !== null) {
            $query->where('subject_id', $subjectId);
        }

        return $query;
    }

    /**
     * Scope untuk mengurutkan aktivitas dari waktu terbaru.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatestLogs($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Method static untuk mengambil seluruh riwayat aktivitas diurutkan dari terbaru.
     *
     * @param int|null $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllLogs(?int $limit = null)
    {
        $query = static::orderBy('created_at', 'desc');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Method static untuk mengambil aktivitas berdasarkan admin, diurutkan terbaru.
     *
     * @param int $adminId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByAdmin(int $adminId)
    {
        return static::byAdmin($adminId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Method static untuk mengambil aktivitas berdasarkan jenis aktivitas, diurutkan terbaru.
     *
     * @param string $activityType
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByType(string $activityType)
    {
        return static::byType($activityType)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Method static untuk mengambil aktivitas berdasarkan subjek/objek.
     *
     * @param string $subjectType
     * @param int|null $subjectId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getBySubject(string $subjectType, ?int $subjectId = null)
    {
        return static::bySubject($subjectType, $subjectId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
