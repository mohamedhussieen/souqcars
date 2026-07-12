<?php

namespace App\Models;

use App\Enums\BodyType;
use App\Enums\CarCondition;
use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\PaymentType;
use App\Enums\SellerType;
use App\Enums\Transmission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/** Represents a car listing. Seller is a loosely polymorphic pair (seller_type + seller_id) resolving to either a User or a Showroom. */
class Car extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'seller_type',
        'seller_id',
        'brand_id',
        'car_model_id',
        'city_id',
        'color_id',
        'year',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'price',
        'payment_type',
        'mileage',
        'condition',
        'transmission',
        'fuel_type',
        'body_type',
        'owners_count',
        'has_inspection_report',
        'status',
        'rejection_reason',
        'is_featured',
        'views_count',
        'favorites_count',
        'rating_avg',
    ];

    protected function casts(): array
    {
        return [
            'seller_type'           => SellerType::class,
            'year'                  => 'integer',
            'price'                 => 'decimal:2',
            'payment_type'          => PaymentType::class,
            'mileage'               => 'integer',
            'condition'             => CarCondition::class,
            'transmission'          => Transmission::class,
            'fuel_type'             => FuelType::class,
            'body_type'             => BodyType::class,
            'owners_count'          => 'integer',
            'has_inspection_report' => 'boolean',
            'status'                => CarStatus::class,
            'is_featured'           => 'boolean',
            'views_count'           => 'integer',
            'favorites_count'       => 'integer',
            'rating_avg'            => 'decimal:2',
        ];
    }

    /** Returns the brand this car belongs to. */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /** Returns the specific model of this car. */
    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class);
    }

    /** Returns the city this car is listed in. */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /** Returns the color of this car, if set. */
    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    /** Returns all ratings submitted for this car. */
    public function ratings(): HasMany
    {
        return $this->hasMany(CarRating::class);
    }

    /** Returns all favorite records for this car. */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Returns the favorite record belonging to a specific user, if any — intended to be
     * eager-loaded per-request (constrained to the current user) to avoid N+1 is_favorited checks.
     */
    public function favoritedByUser(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Resolves the actual seller model (User or Showroom) based on seller_type + seller_id.
     * This is not a true Eloquent polymorphic relation since seller_type maps to two different
     * models depending on value (User for admin/individual, Showroom for showroom).
     */
    public function seller(): User|Showroom|null
    {
        if ($this->seller_id === null) {
            return null;
        }

        return match ($this->seller_type) {
            SellerType::Showroom => Showroom::find($this->seller_id),
            default => User::find($this->seller_id),
        };
    }

    /** Registers the car_images (gallery, max 10 enforced in service) and inspection_report (single file) media collections. */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('car_images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('inspection_report')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'application/pdf']);
    }

    /** Registers 'thumb' and 'full' conversions for car_images only; inspection_report is served as the original file. */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(300)
            ->quality(80)
            ->performOnCollections('car_images');

        $this->addMediaConversion('full')
            ->width(1200)
            ->quality(85)
            ->performOnCollections('car_images');
    }

    /** Returns the thumb URL of the first car image, or null if no images are uploaded. */
    public function getThumbUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('car_images', 'thumb') ?: null;
    }

    /** Returns the original inspection report file URL, or null if none uploaded. */
    public function getInspectionReportUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('inspection_report') ?: null;
    }
}
