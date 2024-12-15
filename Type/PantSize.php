<?php
/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Reference\Pants\Type;

use BaksDev\Reference\Pants\Type\Sizes\Collection\PantSizeInterface;
use InvalidArgumentException;

final class PantSize
{
    public const string TYPE = 'pants_size_type';

    private PantSizeInterface $size;


    public function __construct(PantSizeInterface|self|string $size)
    {
        if(is_string($size) && class_exists($size))
        {
            $instance = new $size();

            if($instance instanceof PantSizeInterface)
            {
                $this->size = $instance;
                return;
            }
        }

        if($size instanceof PantSizeInterface)
        {
            $this->size = $size;
            return;
        }

        if($size instanceof self)
        {
            $this->size = $size->getPantSize();
            return;
        }

        /** @var PantSizeInterface $declare */
        foreach(self::getDeclared() as $declare)
        {
            if($declare::equals($size))
            {
                $this->size = new $declare;
                return;
            }
        }

        throw new InvalidArgumentException(sprintf('Not found PantSize %s', $size));

    }


    public function __toString(): string
    {
        return $this->size->getvalue();
    }


    /** Возвращает значение ColorsInterface */
    public function getPantSize(): PantSizeInterface
    {
        return $this->size;
    }


    /** Возвращает значение ColorsInterface */
    public function getPantSizeValue(): string
    {
        return $this->size->getValue();
    }


    public static function cases(): array
    {
        $case = [];

        foreach(self::getDeclared() as $key => $size)
        {
            /** @var PantSizeInterface $size */
            $sizes = new $size;
            $case[$sizes::sort().$key] = new self($sizes);
        }

        ksort($case);

        return $case;
    }


    public static function getDeclared(): array
    {
        return array_filter(
            get_declared_classes(),
            static function($className)
                {
                    return in_array(PantSizeInterface::class, class_implements($className), true);
                },
        );
    }


    public function equals(mixed $status): bool
    {
        $status = new self($status);

        return $this->getPantSizeValue() === $status->getPantSizeValue();
    }
}