<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Contracts\JWTSubject; 
class User extends Authenticatable   implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role',
        'logo',
        'logo_top',
        'logo_top_sm',
        'name',
        'email',
        'whatsapp_1',
        'whatsapp_2',
        'perm',
        'username',
        'email_verified_at',
        'password',
        'shw_password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'shw_password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    
	public function getJWTIdentifier()
    {
        return $this->getKey();
    }

	public function getJWTCustomClaims()
    {
        return [];
    }

   
    
    public function GenToken($request)
    {

        $login = $request->input('email');
        $user = User::where('email', $login)->orWhere('username', $login)->first();

        if (!$user) { 
            return [
                'message' => 'token_denied -- incorrect access',
                'email' => 'Invalid login credentials',
                'status' => "FAILE",
                "code" => 400
            ];
        }

        $request->validate([
            'password' => 'required|min:8',
        ]);

        if (Auth::attempt(['email' => $user->email, 'password' => $request->password]) || Auth::attempt(['username' => $user->username, 'password' => $request->password])) {
            $user = Auth::user();
            return [
                'token' => JWTAuth::fromUser($user),
                'message' => 'success_token_created',
                'status' => "OK",
                "code" => 200
            ];
        } else {
            return [
                'message' => 'token_denied -- incorrect access',
                'password' => 'Invalid login credentials',
                'status' => "FAILE",
                "code" => 400
            ];
        }
 
    }

}
