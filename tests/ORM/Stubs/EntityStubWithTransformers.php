<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Stubs;

use EoneoPay\Externals\ORM\Entity;
use EoneoPay\Externals\ORM\Traits\HasTransformers;

/**
 * @method null|bool getBool()
 * @method null|\DateTime getDatetime()
 * @method null|string getString()
 * @method self setBool($bool)
 * @method self setDatetime($datetime)
 * @method self setString($string)
 */
class EntityStubWithTransformers extends Entity
{
    use HasTransformers;

    /**
     * @var bool
     */
    protected $bool;

    /**
     * @var \DateTime
     */
    protected $datetime;

    /**
     * @var string
     */
    protected $string;

    /**
     * Serialize entity as an array
     *
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'bool' => $this->bool,
            'datetime' => $this->datetime !== null ? $this->datetime->format('d/m/Y') : null,
            'string' => $this->string
        ];
    }

    /**
     * Transform $bool to bool.
     *
     * @return void
     */
    protected function transformBool(): void
    {
        $this->transformToBool('bool');
    }

    /**
     * Transform $datetime to DateTime.
     *
     * @return void
     */
    protected function transformDatetime(): void
    {
        $this->transformToDateTime('datetime');
    }

    /**
     * Transform $string to string.
     *
     * @return void
     */
    protected function transformString(): void
    {
        $this->transformToString('string');
    }
}
