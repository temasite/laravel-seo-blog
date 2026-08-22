<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Throwable;

#[Fillable(['name', 'description', 'banner', 'slug'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory, HasSlug;

    private const string BANNER_DIRECTORY = 'categories';

    private const string BANNER_DISK = 'public';

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function createWithBanner(array $attributes, ?UploadedFile $banner = null): static
    {
        $bannerPath = $banner ? self::storeBanner($banner) : null;

        if ($bannerPath) {
            $attributes['banner'] = $bannerPath;
        }

        try {
            /** @var static $category */
            $category = static::query()->create($attributes);
        } catch (Throwable $exception) {
            self::deleteBanner($bannerPath);

            throw $exception;
        }

        return $category;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function updateWithBanner(
        array $attributes,
        ?UploadedFile $banner = null,
        bool $removeBanner = false,
    ): void {
        $oldBanner = $this->banner;
        $newBanner = $banner ? self::storeBanner($banner) : null;

        if ($newBanner) {
            $attributes['banner'] = $newBanner;
        } elseif ($removeBanner) {
            $attributes['banner'] = null;
        }

        try {
            $this->fill($attributes)->saveOrFail();
        } catch (Throwable $exception) {
            self::deleteBanner($newBanner);

            throw $exception;
        }

        if ($oldBanner !== $this->banner) {
            self::deleteBanner($oldBanner);
        }
    }

    public function deleteWithBanner(): void
    {
        $banner = $this->banner;

        $this->deleteOrFail();

        self::deleteBanner($banner);
    }

    public static function normalizeSlug(?string $slug, string $name): string
    {
        return Str::slug($slug ?: $name);
    }

    /**
     * @param  Builder<Category>  $query
     */
    public function scopeSearch(Builder $query, ?string $search): void
    {
        $search = trim((string) $search);

        if ($search === '') {
            return;
        }

        $query->where(function (Builder $query) use ($search): void {
            $query
                ->where('name', 'like', '%'.$search.'%')
                ->orWhere('slug', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%');
        });
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function bannerUrl(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->banner
                ? Storage::disk(self::BANNER_DISK)->url($this->banner)
                : null,
        );
    }

    private static function storeBanner(UploadedFile $banner): string
    {
        $path = $banner->store(self::BANNER_DIRECTORY, self::BANNER_DISK);

        if (! is_string($path)) {
            throw new RuntimeException('The category banner could not be stored.');
        }

        return $path;
    }

    private static function deleteBanner(?string $banner): void
    {
        if ($banner) {
            Storage::disk(self::BANNER_DISK)->delete($banner);
        }
    }
}
