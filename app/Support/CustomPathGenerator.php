<?php

    namespace App\Support;

    use Spatie\MediaLibrary\MediaCollections\Models\Media;
    use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

    class CustomPathGenerator implements PathGenerator
    {

        public function getPath( Media $media ) : string
        {
            $prefix = $media->model->id;
            $path = "{$prefix}/{$media->id}";

            if ( $media->collection_name === 'avatar' ) {
                $path = "{$prefix}/{$media->collection_name}/{$media->id}";
            } elseif ( $media->model_type === 'App\Models\Ticket' ) {
                $path = "{$prefix}/tickets/{$media->model->id}/{$media->id}";
            } elseif ( $media->model_type === 'App\Models\Solution' ) {
                $ticketId = $media->model->ticket_id;
                $path = "{$prefix}/tickets/{$ticketId}/solutions/{$media->model->id}/{$media->id}";
            }

            return $path;
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