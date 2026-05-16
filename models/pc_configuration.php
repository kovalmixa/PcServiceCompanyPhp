<?php

class PcConfiguration {
    public int $id;
    public string $name;
    public ?string $image_path;
    public array $components = [];

    public function getBrand(): string {
        return !empty($this->components) ? $this->components[0]['brand'] : 'Custom PC';
    }

    public function getPrice(): float {
        $total = 0.0;
        foreach ($this->components as $component) {
            $total += (float)($component['price'] ?? 0);
        }
        return $total;
    }
}