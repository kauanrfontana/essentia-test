<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    protected $fillable = [
        'str_name',
        'str_email',
        'str_phone',
        'str_profile_picture_path',
    ];

    /**
     * Stores the phone as digits only and formats it as (DD) NNNNN-NNNN
     * (or (DD) NNNN-NNNN for landlines) whenever it's read.
     */
    protected function strPhone(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                $digits = (string) $value;

                return match (strlen($digits)) {
                    11 => sprintf('(%s) %s-%s', substr($digits, 0, 2), substr($digits, 2, 5), substr($digits, 7)),
                    10 => sprintf('(%s) %s-%s', substr($digits, 0, 2), substr($digits, 2, 4), substr($digits, 6)),
                    default => $value,
                };
            },
            set: fn (?string $value) => preg_replace('/\D/', '', (string) $value),
        );
    }

    public function profilePictureUrl(): ?string
    {
        if (blank($this->str_profile_picture_path)) {
            return null;
        }

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return $disk->url($this->str_profile_picture_path);
    }

    public function deleteProfilePicture(): void
    {
        if (filled($this->str_profile_picture_path)) {
            Storage::disk('public')->delete($this->str_profile_picture_path);
        }
    }
}
