<?php

/**
 * Helper. Removing empty values (null, '')
 *
 * @param array $associatedValues
 * @return array
 */
function removeEmptyValues(array $associatedValues): array
{
    foreach ($associatedValues as $key => $value) {
        if ($value === null || $value === '') {
            unset($associatedValues[$key]);
        }
    }

    return $associatedValues;
}
