<?php

return [
    'disk_name' => env('MEDIA_DISK', 's3'),

    'max_file_size' => 1024 * 1024 * 10, // 10MB

    'queue_name' => 'media-library',

    'media_model' => App\Models\Media::class,

    'responsive_images' => [
        'width_calculator' => Spatie\MediaLibrary\ResponsiveImages\WidthCalculator\FileSizeOptimizedWidthCalculator::class,
        'use_tiny_placeholders' => true,
        'tiny_placeholder_generator' => Spatie\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\Blurred::class,
    ],

    'path_generator' => Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator::class,
];