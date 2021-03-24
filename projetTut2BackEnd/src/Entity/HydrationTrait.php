<?php

namespace App\Entity;

trait HydrationTrait
{
    public function hydrate(array $data): self
    {
        foreach ($data as $propertyName => $value) {
            $parts = explode('_', $propertyName);
            $parts = array_map('ucfirst', $parts);
            $propertyName = implode('', $parts);

            $setterName = 'set' . $propertyName;
            if (method_exists($this, $setterName)) {
                if ($setterName === 'setReleaseDate'){
                    $value = new \DateTime($value);
                }
                $this->$setterName($value);
            }
        }

        return $this;
    }
}