<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public $avatar;
    public $currentAvatar;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->currentAvatar = $user->avatar;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'max:2048'], // 2MB max
        ]);

        // Handle avatar upload
        if ($this->avatar) {
            // Delete old avatar
            $user->deleteOldAvatar();

            // Store new avatar
            $avatarPath = $this->avatar->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        } else {
            // Keep existing avatar if no new avatar uploaded
            unset($validated['avatar']);
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Update current avatar state
        $this->currentAvatar = $user->avatar;

        // Reset avatar input
        $this->avatar = null;

        // Dispatch events for real-time updates
        $this->dispatch('profile-updated', name: $user->name);
        $this->dispatch('avatar-updated', avatar: $user->avatar_url ?? null);

        // Refresh the page to update sidebar
        $this->js('window.location.reload()');
    }

    /**
     * Remove the current avatar
     */
    public function removeAvatar(): void
    {
        $user = Auth::user();

        if ($user->avatar) {
            $user->deleteOldAvatar();
            $user->update(['avatar' => null]);
            $this->currentAvatar = null;

            $this->dispatch('profile-updated', name: $user->name);
            $this->dispatch('avatar-updated', avatar: null);

            // Refresh the page to update sidebar
            $this->js('window.location.reload()');
        }
    }

    /**
     * Updated hook to handle property changes
     */
    public function updated($property)
    {
        // Don't reset avatar when other properties change
        if ($property !== 'avatar') {
            // Keep current avatar state intact
            return;
        }
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your profile information and photo')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">

            <!-- Name and Email Fields -->
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer"
                                wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>
            <!-- Avatar Section -->
            <div>
                <flux:label class="block mb-4">{{ __('Profile Photo') }}</flux:label>

                <div class="flex items-start space-x-6 mt-3">
                    <!-- Current Avatar Display -->
                    <div class="flex-shrink-0">
                        @if ($currentAvatar && Storage::disk('public')->exists($currentAvatar))
                            <img src="{{ Storage::disk('public')->url($currentAvatar) }}" alt="{{ $name }}"
                                class="w-20 h-20 rounded-xl object-cover object-center border-2 border-gray-200 dark:border-gray-700">
                        @else
                            <div
                                class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl flex items-center justify-center text-white font-semibold text-xl border-2 border-gray-200 dark:border-gray-700">
                                {{ auth()->user()->initials() }}
                            </div>
                        @endif
                    </div>

                    <!-- Avatar Controls -->
                    <div class="flex-1 space-y-4">
                        <!-- Custom File Upload Button -->
                        <div class="space-y-3">
                            <div class="relative">
                                <input type="file" wire:model="avatar" accept="image/*" id="avatar-upload"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <label for="avatar-upload"
                                    class="inline-flex items-center justify-center px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-200 focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    {{ __('Choose File') }}
                                </label>
                            </div>

                            <!-- File Upload Status -->
                            <div class="text-sm">
                                @if ($avatar)
                                    <div class="flex items-center text-green-600 dark:text-green-400">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $avatar->getClientOriginalName() }}
                                    </div>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">{{ __('No file chosen') }}</span>
                                @endif
                            </div>

                            @error('avatar')
                                <div class="flex items-center text-red-600 dark:text-red-400 text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Preview New Avatar -->
                        @if ($avatar)
                            <div
                                class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $avatar->temporaryUrl() }}" alt="Preview"
                                        class="w-12 h-12 rounded-lg object-cover object-center border border-blue-300 dark:border-blue-600">
                                    <div>
                                        <p class="text-sm font-medium text-blue-900 dark:text-blue-100">
                                            {{ __('New photo ready to save') }}
                                        </p>
                                        <p class="text-xs text-blue-700 dark:text-blue-300">
                                            {{ $avatar->getClientOriginalName() }}
                                            ({{ number_format($avatar->getSize() / 1024, 1) }} KB)
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-3">
                            @if ($currentAvatar)
                                <flux:button type="button" variant="danger" size="sm" wire:click="removeAvatar"
                                    wire:confirm="Are you sure you want to remove your profile photo?">

                                    {{ __('Remove Photo') }}
                                </flux:button>
                            @endif
                        </div>

                        <!-- File Info -->
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                            <div class="flex items-center text-xs text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('JPG, PNG or WebP. Max size 2MB.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
