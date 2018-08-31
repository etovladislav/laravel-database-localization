<?php
/**
 * Created by PhpStorm.
 * User: etovladislav
 * Date: 28.08.18
 * Time: 17:01
 */


namespace Localization\Traits;


use App\Module\Location\Localization\TranslationBuilder;
use App\Module\Location\Localization\TranslationUtil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Pluralizer;

trait Translatable
{

    /**
     * Сохранение параметров с переводом в две таблицы: базовую и с переводом
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes = [])
    {
        DB::beginTransaction();
        $instance = parent::create($attributes);
        $translationInstance = TranslationUtil::createTranslationModelInstance($attributes, $instance);
        $translationInstance->save();
        DB::commit();
        $instance->setRawAttributes(array_merge($translationInstance->toArray(), $instance->toArray()));
        return $instance;
    }

    /**
     * Возвращает имя базовой таблицы в единственном числе
     * @return string
     */
    protected function getSingularTableName(): string
    {
        return Pluralizer::singular($this->getTable());
    }

    /**
     * Возвращает имя базовой таблицы
     * @return string
     */
    protected function getPluralTableName(): string
    {
        return $this->getTable();
    }

    /**
     * Возвращает внешний ключ для таблицы с переводом
     * @return string
     */
    public function getForeignKeyForTable(): string
    {
        if (!isset($this->translationForeginKey)) {
            return $this->getSingularTableName() . "_id";
        }
        return $this->translationForeginKey;
    }

    /**
     * Возвращает имя таблицы с переводом
     * @return string
     */
    public function getTranslationTableName(): string
    {
        if (!isset($this->translationTableName)) {
            return $this->getSingularTableName() . "_translations";
        }
        return $this->translationTableName;
    }


    public function newEloquentBuilder($query)
    {
        return new TranslationBuilder($query);
    }

    public function getTranslatableFields(): array
    {
        return $this->translationFields;
    }

    abstract function getTable();
}