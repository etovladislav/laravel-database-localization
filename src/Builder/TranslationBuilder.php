<?php
/**
 * Created by PhpStorm.
 * User: etovladislav
 * Date: 29.08.18
 * Time: 15:37
 */

namespace Localization\Builder;


use Structure\Base\Model\Builder;

class TranslationBuilder extends Builder
{
    protected $model;

    public function update(array $values)
    {
        $this->model->fill($values);
        $this->model->setAttribute('id', $values['id']);
        $result = parent::update($this->model->getAttributes());
        $translationInstance = TranslationUtil::createTranslationModelInstance($values, $this->model);
        $translationInstance->whereLanguageId($values['language_id'])
            ->where($this->model->getForeignKeyForTable(), $values['id'])
            ->update($translationInstance->getAttributes());

        return $result;
    }

    public function withTranslation(int $languageId = null)
    {
        if ($languageId == null) {
            $languageId = $this->getCurrentLocale();
        }
        $translationTableName = $this->model->getTranslationTableName();
        $foreignKeyForTable = $this->model->getForeignKeyForTable();
        $baseTableName = $this->model->getTable();

        return $this->leftJoin(
            $this->model->getTranslationTableName(),
            function ($join) use ($translationTableName, $foreignKeyForTable, $baseTableName) {
                $join->on(
                    $baseTableName . '.id',
                    '=',
                    $translationTableName . '.' . $foreignKeyForTable
                )->where($translationTableName . '.language_id', '=', 3);
            }
        )->select(
            $translationTableName . '.*',
            $baseTableName . '.*'
        );
    }

    protected function getCurrentLocale(): int
    {

    }

}