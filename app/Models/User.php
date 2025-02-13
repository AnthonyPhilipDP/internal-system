<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Illuminate\Support\Facades\Storage;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;



class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    use SoftDeletes;
    
    const GUEST = 3;
    const EMPLOYEE = 2;
    const ADMIN = 1;
    
    const LEVEL = [
        self::GUEST => 'Level 3: Guest',
        self::EMPLOYEE => 'Level 2: Employee',
        self::ADMIN => 'Level 1: Administrator',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin() || $this->isEmployee(); 
    }

    public function isAdmin(){
        return $this->level === self::ADMIN;
    }

    public function isEmployee(){
        return $this->level === self::EMPLOYEE;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'avatar_url',
        'username',
        'level',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Customize the authentication credentials.
     *
     * @param array $credentials
     * @return array
     */
    public function getAuthCredentials(array $credentials)
    {
        if (isset($credentials['username'])) {
            return ['username' => $credentials['username'], 'password' => $credentials['password']];
        }
        
        return ['name' => $credentials['name'], 'password' => $credentials['password']];
    }

    public function getFilamentAvatarUrl(): ?string
    {
        // return $this->avatar_url;
        // return $this->avatar ? asset('/storage/' . $this->avatar) : null;
        $default = (asset('images/default avatar.png'));
        $fileSystem = Storage::url(path: $this->avatar_url);
        $avatarColumn = 'avatar_url';
        return $this->$avatarColumn ? $fileSystem : $default;
    }

    protected static function boot()
    {
        parent::boot();

        // Only delete avatar when the model is being force deleted
        static::forceDeleted(function ($user) {
            if ($user->avatar_url) {
                Storage::disk('public')->delete($user->avatar_url);
            }
        });

        // Listen for the updating event to remove the old avatar file
        static::updating(function ($user) {
            if ($user->isDirty('avatar_url')) {
                $oldAvatar = $user->getOriginal('avatar_url');
                if ($oldAvatar) {
                    Storage::disk('public')->delete($oldAvatar);
                }
            }
        });
    }
}
