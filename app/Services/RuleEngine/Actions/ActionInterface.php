<?php

namespace App\Services\RuleEngine\Actions;

interface ActionInterface
{
    /**
     * Interface wajib untuk setiap aksi (THEN) dalam sistem RBL.
     *
     * @param  array|null  $params  Parameter dinamis dari database JSON
     * @param  mixed  $entity  Model Eloquent yang memicu event
     */
    public function execute(?array $params, $entity): void;
}
