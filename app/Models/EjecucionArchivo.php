<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EjecucionArchivo extends Model
{
    protected $fillable = [
        "ejecucion_trazabilidad_id",
        "archivo",
    ];
    protected $appends = ["url_archivo", "url_file", "file", "name", "ext"];

    public function getExtAttribute()
    {
        return "";
    }

    public function getFileAttribute()
    {
        return $this->archivo;
    }

    public function getNameAttribute()
    {
        return $this->archivo;
    }

    public function getUrlArchivoAttribute()
    {
        return asset("files/ejecucions/" . $this->archivo);
    }

    public function getUrlImagenAttribute()
    {
        $archivo = "default.png";
        if ($this->archivo) {
            $archivo = $this->archivo;
        }
        return asset("files/ejecucions/" . $archivo);
    }

    public function getUrlFileAttribute()
    {
        $array_imgs = ["jpg", "jpeg", "png", "webp", "gif", "svg"];
        $ext = $this->getExtensionImagenDB($this->archivo);
        if (in_array($ext, $array_imgs)) {
            return asset("/files/ejecucions/" . $this->archivo);
        }
        return asset("/imgs/attach.png");
    }


    public function getExtensionImagenDB($imagen)
    {
        $array = explode(".", $imagen);
        return $array[1];
    }
}
