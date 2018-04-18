<?php
declare(strict_types=1);

namespace EoneoPay\External\Bridge\Laravel\Interfaces;

use Closure;

interface ValidationRuleInterface
{
    /**
     * Get rule name
     *
     * @return string
     */
    public function getName(): string;
    
    /**
     * Get message replacements
     *
     * @return \Closure
     */
    public function getReplacements(): Closure;

    /**
     * Get the validation rule closure
     *
     * @return \Closure
     */
    public function getRule(): Closure;
}
