<?php

if (! function_exists('fcfa')) {
    /**
     * Formate un montant en francs CFA : 15000 → « 15 000 FCFA ».
     */
    function fcfa(int|float|null $montant): string
    {
        return number_format((float) $montant, 0, ',', ' ').' FCFA';
    }
}
