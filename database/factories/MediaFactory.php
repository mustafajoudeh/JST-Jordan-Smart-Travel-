<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Destination;
use App\Models\Event;
use App\Models\Experience;
use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/** @extends Factory<Media> */
class MediaFactory extends Factory
{
    protected $model = Media::class;

    private ?array $existingFile = null;

    protected function newInstance(array $arguments = [])
    {
        $factory = parent::newInstance($arguments);
        $factory->existingFile = $this->existingFile;

        return $factory;
    }

    public function definition(): array
    {
        if ($this->existingFile === null) {
            throw new \LogicException('MediaFactory requires forExistingPublicFile() with a real file on the public disk.');
        }

        return [
            ...$this->existingFile,
            'is_primary' => false,
            'sort_order' => 0,
            'destination_id' => Destination::factory(),
            'activity_id' => null,
            'event_id' => null,
            'experience_id' => null,
            'uploaded_by' => null,
        ];
    }

    public function forExistingPublicFile(string $path): static
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            throw new \InvalidArgumentException("Media file does not exist on the public disk: {$path}");
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mimeType = match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            default => null,
        };

        if ($mimeType === null || $disk->size($path) < 1) {
            throw new \InvalidArgumentException('Media must be a non-empty JPEG, PNG, or WebP file.');
        }

        $this->existingFile = [
            'file_path' => $path,
            'mime_type' => $mimeType,
            'file_size_bytes' => $disk->size($path),
        ];

        return $this;
    }

    public function primary(): static
    {
        return $this->state(['is_primary' => true]);
    }

    public function forActivity(): static
    {
        return $this->state([
            'destination_id' => null,
            'activity_id' => Activity::factory(),
            'event_id' => null,
            'experience_id' => null,
        ]);
    }

    public function forEvent(): static
    {
        return $this->state([
            'destination_id' => null,
            'activity_id' => null,
            'event_id' => Event::factory(),
            'experience_id' => null,
        ]);
    }

    public function forExperience(): static
    {
        return $this->state([
            'destination_id' => null,
            'activity_id' => null,
            'event_id' => null,
            'experience_id' => Experience::factory(),
        ]);
    }
}
