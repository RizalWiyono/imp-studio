<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SystemSetting extends Model
{
    use HasFactory;

    protected $table = 'system_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    // Konstanta untuk tipe setting
    const TYPE_STRING = 'STRING';
    const TYPE_INTEGER = 'INTEGER';
    const TYPE_BOOLEAN = 'BOOLEAN';
    const TYPE_JSON = 'JSON';
    const TYPE_URL = 'URL';

    // Konstanta untuk status
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';
    const STATUS_ARCHIVED = 'ARCHIVED';
    const STATUS_DELETED = 'DELETED';

    /**
     * Cast attributes to native types
     */
    protected $casts = [
        'value' => 'string', // Will be cast based on type
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
                $model->updated_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });

        static::created(function ($model) {
            $ip = request()?->ip();
            $agent = request()?->userAgent();

            ActivityLog::create([
                'user_id' => auth()->check() ? auth()->id() : null,
                'action' => 'CREATED_SYSTEM_SETTING',
                'target_table' => 'system_settings',
                'target_id' => $model->id,
                'description' => 'System setting successfully created',
                'ip_address' => $ip,
                'user_agent' => $agent,
                'metadata' => json_encode([
                    'new' => $model->getAttributes(),
                ])
            ]);
        });

        static::updated(function ($model) {
            $ip = request()?->ip();
            $agent = request()?->userAgent();
            $changes = $model->getChanges();
            $original = $model->getOriginal();

            ActivityLog::create([
                'user_id' => auth()->check() ? auth()->id() : null,
                'action' => 'UPDATED_SYSTEM_SETTING',
                'target_table' => 'system_settings',
                'target_id' => $model->id,
                'description' => 'System setting successfully updated',
                'ip_address' => $ip,
                'user_agent' => $agent,
                'metadata' => json_encode([
                    'old' => $original,
                    'changes' => $changes,
                ])
            ]);
        });

        static::deleted(function ($model) {
            $ip = request()?->ip();
            $agent = request()?->userAgent();

            ActivityLog::create([
                'user_id' => auth()->check() ? auth()->id() : null,
                'action' => 'DELETED_SYSTEM_SETTING',
                'target_table' => 'system_settings',
                'target_id' => $model->id,
                'description' => 'System setting successfully deleted',
                'ip_address' => $ip,
                'user_agent' => $agent,
                'metadata' => json_encode([
                    'deleted' => $model->getOriginal(),
                ])
            ]);
        });
    }

    /**
     * Get the creator of this setting.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the last updater of this setting.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get a typed value based on the setting type.
     *
     * @return mixed
     */
    public function getTypedValueAttribute()
    {
        switch ($this->type) {
            case self::TYPE_INTEGER:
                return (int) $this->value;
            case self::TYPE_BOOLEAN:
                return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
            case self::TYPE_JSON:
                return json_decode($this->value, true);
            case self::TYPE_URL:
            case self::TYPE_STRING:
            default:
                return $this->value;
        }
    }

    /**
     * Set the value with appropriate type casting
     *
     * @param mixed $value
     * @return void
     */
    public function setValueAttribute($value)
    {
        if (isset($this->attributes['type'])) {
            switch ($this->attributes['type']) {
                case self::TYPE_INTEGER:
                    $this->attributes['value'] = (int) $value;
                    break;
                case self::TYPE_BOOLEAN:
                    $this->attributes['value'] = $value ? '1' : '0';
                    break;
                case self::TYPE_JSON:
                    $this->attributes['value'] = is_array($value) || is_object($value) ? json_encode($value) : $value;
                    break;
                case self::TYPE_URL:
                case self::TYPE_STRING:
                default:
                    $this->attributes['value'] = (string) $value;
                    break;
            }
        } else {
            $this->attributes['value'] = (string) $value;
        }
    }

    /**
     * Static method to get a system setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue($key, $default = null)
    {
        $setting = static::where('key', $key)
                        ->where('status', self::STATUS_ACTIVE)
                        ->first();
        
        if (!$setting) {
            return $default;
        }
        
        return $setting->typed_value;
    }

    /**
     * Static method to set a system setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @param string $description
     * @return SystemSetting
     */
    public static function setValue($key, $value, $type = self::TYPE_STRING, $description = null)
    {
        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = $value;
        $setting->type = $type;
        
        if ($description) {
            $setting->description = $description;
        }
        
        if (!isset($setting->status)) {
            $setting->status = self::STATUS_ACTIVE;
        }
        
        $setting->save();
        
        return $setting;
    }

}
