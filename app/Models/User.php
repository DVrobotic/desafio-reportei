<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'cpf',
        'dateBirth',
        'password',
        'type',
        'profile_path'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public static function saveImg($data, $name, $diretorio, $imgAntiga = '') {
        if(isset($data[$name]) && is_file($data[$name])){
            $imgName = $data[$name]->getClientOriginalName();
            $imgName = hash('sha256', $imgName . strval(time())) . '.' . $data[$name]->getClientOriginalExtension();
            User::deleteImg($imgAntiga, $diretorio);
            $data[$name]->storeAs($diretorio, $imgName);
            $data[$name] = $imgName;
        }else{
            unset($data[$name]);
        }

        return $data;
    }
    public static function deleteImg($imgName, $diretorio) {
        if($imgName != '' && $imgName != 'profile_default.png'){
            Storage::delete($diretorio . $imgName);
        }
    }

    public static function verifyUpdatePassword($data){
        if($data['password']){
            $data['password'] = \bcrypt($data['password']);
            unset($data['password_confirmation']);
        }else{
            unset($data['password'], $data['password_confirmation']);
        }
        return $data;
    }
}
