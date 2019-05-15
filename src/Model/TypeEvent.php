<?php
/**
 * Created by PhpStorm.
 * User: werd
 * Date: 15/05/19
 * Time: 14:20
 */

namespace App\Model;


use App\Entity\Event;
use App\Entity\Events\EventHumidity;
use App\Entity\Events\EventTemperature;

abstract class TypeEvent
{
    public const Event = Event::class;
    public const Humidity = EventHumidity::class;
    public const Temperature = EventTemperature::class;
    public const UNKNOWN = 'unknown';

    /** @var array  */
    protected static $longName = [
        self::Event => 'Event',
        self::Humidity => 'Humidity',
        self::Temperature => 'Temperature',
        self::UNKNOWN => 'UNKNOWN',
    ];

    /**
     * @param string $input
     * @return string
     */
    public static function getName(string $input): string
    {
        return self::getShortName($input);
    }

    /**
     * @param string $shortName
     * @return string
     */
    public static function getLongName(string $shortName): string
    {
        foreach (static::$longName as $longNameTmp => $shortNameTmp) {
            if ($shortName === $shortNameTmp) {
                return $longNameTmp;
            }
        }
        return static::$longName[self::UNKNOWN];
    }

    /**
     * @param string $longName
     * @return string
     */
    public static function getShortName(string $longName): string
    {
        if (!isset(static::$longName[$longName])) {
            return static::$longName[static::UNKNOWN];
        }
        return static::$longName[$longName];
    }

    /**
     * @return array<integer>
     */
    public static function getAvailable(): array
    {
        return get_class_vars(TypeEvent::class)['longName'];
    }

    /**
     * @return array<integer>
     */
    public static function getPreferred(): array
    {
        return [
            self::Event,
        ];
    }

    /**
     * @return array
     */
    public static function buildFormType(array $preferred=null): array
    {
        $ret = array(
            'required' => true,
            'choices' => array_flip(self::getAvailable()),
               // return array_flip(self::getAvailable());

//            'preferred_choices' => self::getPreferred(),
            'choice_label' => function($choice, $key, $value) {
                if ($choice !== $value) {
                    $a = 1;
                }  else {
                    $a = 2;

                }
                return self::getName($value);
            },
        );
//        if ($preferred !== null) {
//            $ret['preferred_choices'] = self::getShortName($preferred[0]);
//        }
        return $ret;
        /**
         * The option "choices_as_values" does not exist.
         * Defined options are: "action", "allow_extra_fields", "allow_file_upload", "attr", "auto_initialize",
         * "block_name", "by_reference", "choice_attr", "choice_label", "choice_loader", "choice_name",
         * "choice_translation_domain", "choice_value", "choices", "compound", "constraints",
         * "csrf_field_name", "csrf_message", "csrf_protection", "csrf_token_id", "csrf_token_manager",
         * "data", "data_class", "disabled", "empty_data", "error_bubbling", "error_mapping", "expanded",
         * "extra_fields_message", "group_by", "help", "help_attr", "inherit_data",
         * "invalid_message", "invalid_message_parameters", "label", "label_attr", "label_format", "mapped",
         * "method", "multiple", "placeholder", "post_max_size_message", "preferred_choices", "property_path",
         * "required", "translation_domain", "trim", "upload_max_size_message", "validation_groups".
         */
    }
}