<?php
if (empty($registrations) || empty($final) || !$final['final'])
{
    ?>
    <p>
        Du kan herunder afslutte din tilmelding. Hvis tilmeldingen afsluttes, vil der ikke kunne laves flere ændringer, unden at kontakte
        lejrledelsen på <a href="mailto:info@djurslejren.dk">info@djurslejren.dk</a>.<br/>
        Din tilmelding skal afsluttes inden lejren, og det resterende beløb (<span class="total"></span>) skal indbetales på konto <b><?= $account ?></b>.<br/>
        <input type="button" id="markFinal" value="Afslut tilmelding" />
    </p>
<?php } elseif (empty($admin)) { ?>
    <p>
        <?php $finalTime = $final['time']; /* @var DateTime $finalTime */ ?>
        Du har afsluttet din tilmelding <strong>d. <?= $finalTime->format('j/n') ?> kl. <?= $finalTime->format('H:i') ?></strong>, og kan derfor ikke foretage ændringer.<br/>
        Hvis det er nødvendigt alligevel at foretage ændringer, kontakt da lejrledelsen på <a href="mailto:info@djurslejren.dk">info@djurslejren.dk</a>.<br/>
    </p>
<?php } else { ?>
    <p>
        <?php $finalTime = $final['time']; /* @var DateTime $finalTime */ ?>
        Tilmeldingen blev afsluttet <strong>d. <?= $finalTime->format('j/n') ?> kl. <?= $finalTime->format('H:i') ?></strong>, og gruppen kan derfor ikke foretage ændringer.<br/>
        Hvis det er nødvendigt alligevel at foretage ændringer, kan denne genåbnes.<br/>
        <input type="button" id="unmarkFinal" value="Genåbn tilmelding" />
    </p>
<?php } ?>

<h2>Deltagere</h2>
<table>
    <thead>
    <tr>
        <?php
        if (empty($final) || !$final['final']) {
        ?>
            <th>Fjern</th>
        <?php } ?>
        <th>Navn</th>
        <th>Fødselsdato</th>
        <th>Særlige behov</th>
        <th>Aldersgruppe</th>
        <th>Længde</th>
        <th class="price">Lejrens pris</th>
        <th class="price">1. rate</th>
        <th class="price">2. rate</th>
        <th class="price">3. rate</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if (!empty($registrations))
    {
        if (!empty($final) && $final['final'])
        {
            $colspan = 5;
            foreach ($registrations as $i => $r)
            {
                ?>
                <tr>
                    <td><?= $r['name'] ?></td>
                    <td><?= date("d/m-Y", strtotime($r['birthdate'])) ?></td>
                    <td><?= nl2br($r['needs'])?></td>
                    <td>
                        <?php
                        $selectedAge = null;
                        foreach ($ages as $a)
                        {
                            if ($r['age'] == $a['key'])
                            {
                                $selectedAge = $a;
                                echo $a['title'];
                            }
                            ?>
                        <?php
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($r['length'] == 'half')
                        {
                            echo "For de yngste";
                        }
                        else
                        {
                            echo "Hele lejren";
                        }
                        ?>
                    </td>
                    <td class="price" data-price="<?= $selectedAge['price'][$r['length']] ?>"><?= number_format($selectedAge['price'][$r['length']], 0, ',', '.') ?></td>
                    <td class="rate" data-rate="1" data-price="<?= $selectedAge['rate'][$r['length']][0] ?>"><?= number_format($selectedAge['rate'][$r['length']][0], 0, ',', '.') ?>
                    <td class="rate" data-rate="2" data-price="<?= $selectedAge['rate'][$r['length']][1] ?>"><?= number_format($selectedAge['rate'][$r['length']][1], 0, ',', '.') ?>
                    <td class="rate" data-rate="3" data-price="<?= $selectedAge['rate'][$r['length']][2] ?>"><?= number_format($selectedAge['rate'][$r['length']][2], 0, ',', '.') ?>
                </tr>
            <?php
            }
        }
        else
        {
            $colspan = 6;
            foreach ($registrations as $i => $r)
            {
                ?>
                <tr data-id="<?= $i ?>">
                    <td><input type="button" class="remove" value="Fjern" /></td>
                    <td><input type="text" name="name[]" value="<?= $r['name'] ?>" class="name" /></td>
                    <td><input type="text" name="birthdate[]" value="<?= date("d/m-Y", strtotime($r['birthdate'])) ?>" class="birthdate" /></td>
                    <td><textarea name="needs[]" class="needs"><?= $r['needs'] ?></textarea></td>
                    <td>
                        <select name="age[]" class="age">
                            <?php
                            $selectedAge = null;
                            foreach ($ages as $a)
                            {
                                if ($r['age'] == $a['key'])
                                {
                                    $s = 'selected="selected"';
                                    $selectedAge = $a;
                                }
                                else
                                {
                                    $s = '';
                                }
                                ?>
                                <option value="<?= $a['key'] ?>" <?= $s ?>><?= $a['title'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <?php
                        if ($r['length'] == 'half')
                        {
                            $sH = 'selected="selected"';
                            $sF = '';
                        }
                        else
                        {
                            $sF = 'selected="selected"';
                            $sH = '';
                        }
                        ?>
                        <select name="length[]" class="length">
                            <option value="full" <?= $sF ?>>Hele lejren</option>
                            <option value="half" <?= $sH ?>>For de yngste</option>
                        </select>
                    </td>
                    <td class="price" data-price="<?= $selectedAge['price'][$r['length']] ?>"><?= number_format($selectedAge['price'][$r['length']], 0, ',', '.') ?></td>
                    <td class="rate" data-rate="1" data-price="<?= $selectedAge['rate'][$r['length']][0] ?>"><?= number_format($selectedAge['rate'][$r['length']][0], 0, ',', '.') ?>
                    <td class="rate" data-rate="2" data-price="<?= $selectedAge['rate'][$r['length']][1] ?>"><?= number_format($selectedAge['rate'][$r['length']][1], 0, ',', '.') ?>
                    <td class="rate" data-rate="3" data-price="<?= $selectedAge['rate'][$r['length']][2] ?>"><?= number_format($selectedAge['rate'][$r['length']][2], 0, ',', '.') ?>
                </tr>
            <?php
            }
        }
    }
    if (empty($final) || !$final['final']) {
    ?>
        <tr class="new">
            <td><input type="button" class="remove" value="Fjern" /></td>
            <td><input type="text" name="name[]" class="name" /></td>
            <td><input type="text" name="birthdate[]" class="birthdate" /></td>
            <td><textarea name="needs[]" class="needs"></textarea></td>
            <td>
                <select name="age[]" class="age">
                    <option value="" selected="selected">Vælg aldersgruppe</option>
                    <?php
                    foreach ($ages as $a)
                    {
                        ?>
                        <option value="<?= $a['key'] ?>"><?= $a['title'] ?></option>
                    <?php
                    }
                    ?>
                </select>
            </td>
            <td>
                <select name="length[]" class="length">
                    <option value="full" selected="selected">Hele lejren</option>
                    <option value="half">For de yngste</option>
                </select>
            </td>
            <td class="price" data-price="0">0</td>
            <td class="rate" data-rate="1" data-price="0">0</td>
            <td class="rate" data-rate="2" data-price="0">0</td>
            <td class="rate" data-rate="3" data-price="0">0</td>
        </tr>
    <?php
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="<?= $colspan ?>" class="totalhead">Samlet betaling:</td>
        <td class="total price"></td>
        <td class="total rate" data-rate="1"></td>
        <td class="total rate" data-rate="2"></td>
        <td class="total rate" data-rate="3"></td>
    </tr>
    </tfoot>
</table>
<?php
if (empty($final) || !$final['final']) {
?>
<span class="message"></span>
<input type="submit" value="Gem tilmelding" />
<?php
}
?>