<?php

namespace App\Entity;
use DateTime;

trait HydrationTrait
{
    /**
     * @param array $data
     * @return $this
     */
    public function hydrate(array $data): self
    {
        foreach ($data as $propertyName => $value) {
            $parts = explode('_', $propertyName);
            $parts = array_map('ucfirst', $parts);
            $propertyName = implode('', $parts);

            $setterName = 'set' . $propertyName;
            if (method_exists($this, $setterName)) {
                if ($setterName === 'setReleaseDate'){
                    $value = new DateTime($value);
                }
                if (($setterName === 'setDeveloper') && (gettype($value) !== "array")){
                    $value = [$value];
                }
                $this->$setterName($value);
            }
        }

        return $this;
    }
}