<?php
    declare( strict_types = 1 );

    namespace App\Support;

    use Spatie\MediaLibrary\MediaCollections\Models\Media;
    use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

    class CustomPathGenerator implements PathGenerator
    {

        public function getPath( Media $media ) : string
        {
            $model_id = $media->model->id;
            return "{$media->collection_name}/{$model_id}/{$media->id}";
        }

        public function getPathForConversions( Media $media ) : string
        {
            return $this->getPath( $media ) . '/conversions';
        }

        public function getPathForResponsiveImages( Media $media ) : string
        {
            return $this->getPath( $media ) . '/responsive-images';
        }

    }