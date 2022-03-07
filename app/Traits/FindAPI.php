<?php

namespace App\Traits;

trait FindAPI
{
        public function findResource($resource)
        {
            if($resource->exists())
            {
                return [
                    'status' => true,
                    'Message' => $resource->get(),
                    'count' => $resource->count(),
                ];
            }
            else
            {
                return [
                    'status' => false,
                    'Message' => "Resource Not Found",
                ];
            }

        }

        public function updateResource($resource,$data)
        {
            if($resource->exists())
            {
                $resource->update($data);
                return [
                    'status' => true,
                    'Message' => "Resource Updated Successfully",
                ];
            }
            else
            {
                return [
                    'status' => false,
                    'Message' => "Resource Not Found",
                ];
            }
        }

        public function destroyResource($resource)
        {
            if($resource->exists())
            {
                $resource->delete();
                return [
                    'status' => true,
                    'Message' => "Resource Deleted Successfully",
                ];
            }
            else
            {
                return [
                    'status' => false,
                    'Message' => "Resource Not Found",
                ];
            }
        }

}
