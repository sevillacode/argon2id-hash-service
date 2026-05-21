<?php
class RateLimiter {
    private string $filePath;

    public function __construct(string $filePath) {
        $this->filePath = $filePath;
    }

    public function allowAccess(string $ip): bool {
        $now = time();
        $data = [];

        // Leer los registros del archivo JSON si existe
        if (file_exists($this->filePath)) {
            $content = file_get_contents($this->filePath);
            if ($content) {
                $data = json_decode($content, true) ?? [];
            }
        }

        // Limpieza automática: borrar registros de más de 2 segundos para no llenar el archivo
        foreach ($data as $storedIp => $lastAccess) {
            if ($now - $lastAccess >= 2) {
                unset($data[$storedIp]);
            }
        }

        // Comprobar la IP actual
        if (isset($data[$ip])) {
            $lastAccess = (int)$data[$ip];
            if ($now - $lastAccess < 1) {
                // Guardar la limpieza antes de rechazar la petición
                file_put_contents($this->filePath, json_encode($data), LOCK_EX);
                return false; // Límite excedido (1 acceso por segundo)
            }
        }

        // Actualizar el acceso de esta IP con la hora actual
        $data[$ip] = $now;

        // Guardar los cambios en el archivo
        file_put_contents($this->filePath, json_encode($data), LOCK_EX);

        return true;
    }
}
