<?php

namespace  App\Filament\Auth;

use Filament\Forms\Form;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseAuth;
use Illuminate\Validation\ValidationException;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
 
class Login extends BaseAuth
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getLoginFormComponent(), 
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }
 
    protected function getLoginFormComponent(): Component 
    {
        return TextInput::make('login')
            ->label('Name or Username')
            ->required()
            ->maxLength(255)
            ->minLength(2)
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    } 

    //This applies with email and username only
    // protected function getCredentialsFromFormData(array $data): array
    // {
    //     $login_type = filter_var($data['login'], FILTER_VALIDATE_EMAIL ) ? 'email' : 'name';

    //     return [
    //         $login_type => $data['login'],
    //         'password'  => $data['password'],
    //     ];
    // }

    //For future reference
    //In config/auth.php, see line 75
    protected function getCredentialsFromFormData(array $data): array
    {
        // Check if any user exists with this username
        $userByUsername = DB::table('users')
            ->where('username', $data['login'])
            ->exists();

        // If found by username, use username credentials
        if ($userByUsername) {
            return [
                'username' => $data['login'],
                'password' => $data['password'],
            ];
        }

        // Otherwise, use name credentials
        return [
            'name' => $data['login'],
            'password' => $data['password'],
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $credentials = $this->getCredentialsFromFormData($this->form->getState());
            
            if (! auth()->attempt($credentials)) {
                $this->throwFailureValidationException();
            }

            return app(LoginResponse::class);
        } catch (ValidationException $e) {
            throw $e;
        }
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

    //Comment starts here
    //This has better error handling and security
    // protected function getCredentialsFromFormData(array $data): array
    // {
    //     try {
    //         // Trim whitespace from login input
    //         $loginInput = trim($data['login']);

    //         // Check if any user exists with this username
    //         $userByUsername = DB::table('users')
    //             ->where('username', $loginInput)
    //             ->exists();

    //         // If found by username, use username credentials
    //         if ($userByUsername) {
    //             return [
    //                 'username' => $loginInput,
    //                 'password' => $data['password'],
    //             ];
    //         }

    //         // Otherwise, use name credentials
    //         return [
    //             'name' => $loginInput,
    //             'password' => $data['password'],
    //         ];
    //     } catch (QueryException $e) {
    //         Log::error('Database error during login credential check: ' . $e->getMessage());
    //         throw ValidationException::withMessages([
    //             'data.login' => __('An error occurred while processing your login request.'),
    //         ]);
    //     }
    // }

    // public function authenticate(): ?LoginResponse
    // {
    //     try {
    //         $credentials = $this->getCredentialsFromFormData($this->form->getState());
            
    //         // Add rate limiting check
    //         if ($this->hasTooManyLoginAttempts()) {
    //             $this->fireLockoutEvent();
    //             $this->throwFailureValidationException();
    //         }

    //         if (! auth()->attempt($credentials)) {
    //             $this->incrementLoginAttempts();
    //             $this->throwFailureValidationException();
    //         }

    //         $this->clearLoginAttempts();
    //         return app(LoginResponse::class);

    //     } catch (ValidationException $e) {
    //         throw $e;
    //     } catch (\Exception $e) {
    //         Log::error('Unexpected error during authentication: ' . $e->getMessage());
    //         throw ValidationException::withMessages([
    //             'data.login' => __('An unexpected error occurred during login.'),
    //         ]);
    //     }
    // }

    // protected function throwFailureValidationException(): never
    // {
    //     throw ValidationException::withMessages([
    //         'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
    //     ]);
    // }

    // // Add rate limiting methods
    // protected function hasTooManyLoginAttempts(): bool
    // {
    //     return app()->get('limiter')->tooManyAttempts(
    //         $this->throttleKey(),
    //         5 // maximum attempts
    //     );
    // }

    // protected function incrementLoginAttempts(): void
    // {
    //     app()->get('limiter')->hit($this->throttleKey(), 60 * 10); // 10 minutes
    // }

    // protected function clearLoginAttempts(): void
    // {
    //     app()->get('limiter')->clear($this->throttleKey());
    // }

    // protected function throttleKey(): string
    // {
    //     return 'login_throttle_' . request()->ip();
    // }

    //Comment ends here
}