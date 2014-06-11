<?php
/**
 * Security check - make sure we were included by the main plugin file
 */
if (!defined('REGISTRATION_LOAD_VIEW')) exit();
?>
<p>
	Velkommen til tilmeldingen til Djurslejren 2014.<br/>
	<?php if (empty($admin)) { ?>
		Du er logget på som: <strong><?= $user->get('display_name') ?></strong>. <?php wp_loginout('/'.REGISTRATION_PAGE_CONTROLLER_NAME); ?>
	<?php } else { ?>
		Du er logget på som: <strong><?= $admin->get('display_name') ?></strong>. <?php wp_loginout('/'.REGISTRATION_PAGE_CONTROLLER_NAME); ?><br/>
		Du ser i øjeblikket tilmeldinger for <strong><?= $user->get('display_name') ?></strong>. <a href="/<?= REGISTRATION_PAGE_CONTROLLER_NAME ?>/oversigt">Tilbage til oversigten</a>.
	<?php } ?>
</p>

<h2>Vejledning</h2>
<p>
	For at udfylde tilmeldingen skal du blot udfylde de relevante felter herunder.<br/>
	Når du har udfyldt felterne, trykker du på knappen "<b>Gem tilmelding</b>".<br/>
</p>

<p>
	Du kan, indtil tilmeldingen lukker, altid komme tilbage og rette i oplysningerne.
</p>

<h3>Lejrens længde</h3>
<p>
	Når du udfylder fødselsdatoen på en spejder, vil den relevante aldersgruppe automatisk blive valgt.<br/>
	Denne er dog kun vejledende, og kan frit ændres efterfølgende. Du kan vælge imellem to længder på lejren.<br/>
	<ol>
		<li>Hele lejren</li>
		<li>For de yngste</li>
	</ol>
	<b>Hele lejren</b> betyder, at spejderen deltager i hele lejrugen (19-26. juli).<br/>
	<b>For de yngste</b> betyder, at spejderen deltager i den del af lejren, som er målrettet de yngste deltagere (19-22. juli).<br/>
	De yngste deltagere (<?= $ages[1]['title'] ?>) kan selvfølgelig også deltage på hele lejren. Der vil dog ikke være et målrettet program i resten af lejrugen.
</p>

<h3>Betaling</h3>
<p>
	Du skal senest den <b><?= strftime("%#d. %B %Y", strtotime($rates[0])) ?></b> indbetale <span class="rate" data-rate="1"></span> til konto <b><?= $account ?></b>.<br/>
	Du skal senest den <b><?= strftime("%#d. %B %Y", strtotime($rates[1])) ?></b> indbetale <span class="rate" data-rate="2"></span> til konto <b><?= $account ?></b>.<br/>
	Du skal senest den <b><?= strftime("%#d. %B %Y", strtotime($rates[2])) ?></b> indbetale <span class="rate" data-rate="3"></span> til konto <b><?= $account ?></b>.<br/>
	I alt skal du senest den <b><?= strftime("%#d. %B %Y", strtotime($rates[2])) ?></b> have indbetalt <span class="total"></span>.
</p>
<hr>
<form id="registration-update" data-userid="<?= $user->get('id') ?>">
	<fieldset>
        <h2>E-mail</h2>
		<p>
            For nemmere at kunne kontakte dig, skal vi bruge en e-mail adresse, indtast den venligst her:<br/>
			<label for="email">E-mail:</label> <input type="text" name="email" id="email" value="<?= $user->get('user_email') ?>" class="email" />
		</p>

        <h2>Natløb/aftenløb</h2>
        <p>
            På Djurslejren er der både et aftenløb mandag aften og et natløb torsdag nat.<br/>
            Da det er forskelligt fra gruppe til gruppe, hvor skræmmende et løb skal/må være, og hvilken aldersgruppe man er på lejr med, bedes i udfylde hvor mange deltagere i skal have med på natløb og aftenløb.<br/>
            <label for="evening">Aftenløb:</label> <input type="number" name="evening" id="evening" value="<?= $race['evening'] ?>" class="evening" /><br/>
            <label for="night">Natløb</label> <input type="number" name="night" id="night" value="<?= $race['night'] ?>" class="night" /><br/>
        </p>

        <h2>Afslut tilmelding</h2>
        <span class="message"></span>

        <div class="register-status">
            <?php
            include('register_table.php');
            ?>
        </div>
	</fieldset>
</form>