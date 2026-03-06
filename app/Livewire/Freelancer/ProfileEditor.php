<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\FreelancerProfile;
use App\Models\Portfolio;
use Auth;
use Storage;

class ProfileEditor extends Component
{
    use WithFileUploads;

    public $headline;
    public $profilePhoto;
    public $currentProfilePhoto;
    public $summary;
    public $hourly_rate;
    public $currency = 'USD';
    public $availability_status = 'available';
    public $skills = '';
    public $languages = '';
    public $portfolioFiles = [];
    // user fields
    public $name;
    public $email;
    public $phone;
    public $location;
    public $bio;
    // metrics
    public $metrics_completed_projects;
    public $metrics_rating;
    public $metrics_total_earnings;
    public $kyc_status;

    public function mount()
    {
        $user = Auth::user();
        $this->currentProfilePhoto = $user->profile_photo;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->location = $user->location;
        $this->bio = $user->bio;
        $profile = $user->freelancerProfile;
        if ($profile) {
            $this->headline = $profile->headline;
            $this->summary = $profile->summary;
            $this->hourly_rate = $profile->hourly_rate;
            $this->currency = $profile->currency;
            $this->availability_status = $profile->availability_status;
            $this->skills = is_array($profile->skills) ? implode(',', $profile->skills) : $profile->skills;
            $this->languages = is_array($profile->languages) ? implode(',', $profile->languages) : $profile->languages;
            $metrics = is_array($profile->metrics) ? $profile->metrics : (json_decode($profile->metrics, true) ?? []);
            $this->metrics_completed_projects = $metrics['completed_projects'] ?? null;
            $this->metrics_rating = $metrics['rating'] ?? null;
            $this->metrics_total_earnings = $metrics['total_earnings'] ?? null;
            $this->kyc_status = $profile->kyc_status ?? 'pending';
        }
    }

    protected function rules()
    {
        return [
            'profilePhoto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|dimensions:min_width=200,min_height=200|max:8192',
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:5000',
            'headline' => 'nullable|string|max:120',
            'summary' => 'nullable|string|max:5000',
            'hourly_rate' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:8',
            'availability_status' => 'nullable|string|in:available,unavailable,busy',
            'skills' => 'nullable|string|max:1000',
            'languages' => 'nullable|string|max:500',
            'metrics_completed_projects' => 'nullable|integer|min:0',
            'metrics_rating' => 'nullable|numeric|min:0|max:5',
            'metrics_total_earnings' => 'nullable|numeric|min:0',
            'portfolioFiles.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,mp4,zip|max:8192',
        ];
    }

    public function saveProfile()
    {
        $this->validate();
        $user = Auth::user();
        // update user fields
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'location' => $this->location,
            'bio' => $this->bio,
        ]);

        // handle profile photo upload
        if ($this->profilePhoto) {
            $path = $this->profilePhoto->store('avatars', 'public');
            // delete old photo if exists
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $user->profile_photo = $path;
            $user->save();
            $this->currentProfilePhoto = $path;
            $this->profilePhoto = null;
        }
        $profile = FreelancerProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'headline' => $this->headline,
                'summary' => $this->summary,
                'hourly_rate' => $this->hourly_rate,
                'currency' => $this->currency,
                'availability_status' => $this->availability_status,
                'skills' => $this->skills ? array_map('trim', explode(',', $this->skills)) : null,
                'languages' => $this->languages ? array_map('trim', explode(',', $this->languages)) : null,
                'metrics' => [
                    'completed_projects' => $this->metrics_completed_projects ?? 0,
                    'rating' => $this->metrics_rating ?? null,
                    'total_earnings' => $this->metrics_total_earnings ?? 0,
                ],
            ]
        );

        // handle portfolio uploads
        if (!empty($this->portfolioFiles)) {
            foreach ($this->portfolioFiles as $file) {
                $path = $file->store('portfolios', 'public');
                Portfolio::create([
                    'user_id' => $user->id,
                    'title' => Str::limit($this->headline ?? 'Portfolio item', 80),
                    'description' => null,
                    'media_path' => $path,
                    'media_type' => $file->getClientMimeType(),
                    'is_public' => true,
                ]);
            }
            $this->portfolioFiles = [];
        }

        session()->flash('success', 'Perfil salvo com sucesso.');
        $this->emit('profileSaved');
    }

    public function render()
    {
        return view('livewire.freelancer.profile-editor');
    }
}
