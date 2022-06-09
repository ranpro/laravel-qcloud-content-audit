<?php

namespace Overtrue\LaravelQcloudContentAudit\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Overtrue\LaravelQcloudContentAudit\Exceptions\InvalidTextException;
use Overtrue\LaravelQcloudContentAudit\Moderators\Tms;

trait CheckTextWithTms
{
//    protected array $tmsCheckable = [];
//    protected string $tmsCheckStrategy = Tms::DEFAULT_STRATEGY;
//    protected bool $tmsJoinFields = false;

    public static function bootCheckTextWithTms()
    {
        static::saving(
            function (Model $model) {
                /* @var Model|static $model */
                if (empty($model->tmsCheckable ?? [])) {
                    return;
                }

                foreach ($model->getTmsContents() as $content) {
                    if (!\Overtrue\LaravelQcloudContentAudit\Tms::validate($content, $model->tmsCheckStrategy ?? Tms::DEFAULT_STRATEGY)) {
                        throw new InvalidTextException('文本内容不合法，请检查后重试！');
                    }
                }
            }
        );
    }

    public function getTmsContents(): array
    {
        /* @var Model|static $this */
        $attributes = Arr::only($this->getDirty(), $this->tmsCheckable ?? []);

        return ($this->tmsJoinFields ?? true) ? [\join('|', $attributes)] : $attributes;
    }
}
