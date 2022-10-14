<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','author','image'
    ];


    public function hasImage(string $image):bool
    {
        return strpos($this->image, $image) !== false;
    }

    public function findMissingImages(array $images):array
    {
        $missingImages = [];
        $currentImages = $this->image ? explode('|', $this->image) : [];

        foreach($currentImages as $image)
        {
            if(!in_array($image, $images))
            {
                $missingImages[] = $image;
            }
        }

        return $missingImages;
    }
}
