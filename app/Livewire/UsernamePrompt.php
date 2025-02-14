<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;

class UsernamePrompt extends Component implements HasForms
{
    use InteractsWithForms;

    public bool $isOpen = false;
    public ?string $username = '';

    public function mount()
    {
        $user = auth()->user();
        
        if ($user && 
            is_null($user->username) && 
            session()->get('just_logged_in', false) && 
            !session()->get('username_prompt_shown', false)
        ) {
            $this->isOpen = true;
            session()->put('username_prompt_shown', true);
            session()->forget('just_logged_in');
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('username')
                    ->prefix('Enter your desired username:')
                    ->minLength(3)
                    ->maxLength(12)
                    ->unique('users', 'username', ignorable: auth()->user())
            ]);
    }

    public function save()
    {
        $user = auth()->user();
        $data = $this->form->getState();

        $user->update([
            'username' => $data['username']
        ]);

        $this->isOpen = false;

        Notification::make()
            ->title('Wow, nice username!')
            ->body('Your username has been successfully registered. Now you can use it for your login.')
            ->success()
            ->send();
    }

    public function skip()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        $user = auth()->user();
        return view('livewire.username-prompt', [
            'name' => $user ? $user->name : null,
        ]);
    }
}