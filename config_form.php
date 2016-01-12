
<?php
$textcopyrightwarning                   = get_option('textcopyrightwarning');
$textextremewarning                     = get_option('textextremewarning');
$creatorprivatewarning                  = get_option('creatorprivatewarning');
$imagewarning                           = get_option('imagewarning');
$kloekelink                             = get_option('kloekelink');
$motiflink                              = get_option('motiflink');
$subcollectionswithtypes                = get_option('subcollectionswithtypes');
$mediumsearchablefields                 = get_option('mediumsearchablefields');

$view = get_view();
?>

<div class="field">
    <?php echo $view->formLabel('textcopyrightwarning', 'Reply-From Email'); ?>
    <div class="inputs">
        <?php echo $view->formTextarea('textcopyrightwarning', $textcopyrightwarning, array('rows' => '6', 'cols' => '60', 'class' => 'textinput')); ?>
        <p class="explanation">
            De waarschuwing die gegeven wordt wanneer de text niet online mag komen vanwege auteursrecht.
        </p>
    </div>
</div>

<div class="field">
    <?php echo $view->formLabel('textextremewarning', 'Extreme inhoud tekst waarschuwing'); ?>
    <div class="inputs">
        <?php echo $view->formTextarea('textextremewarning', $textextremewarning, array('rows' => '6', 'cols' => '60', 'class' => 'textinput')); ?>
        <p class="explanation">
            De waarschuwing die gegeven wordt wanneer de text niet online mag komen vanwege extreme inhoud.
        </p>
    </div>
</div>

<div class="field">
    <?php echo $view->formLabel('creatorprivatewarning', 'Verteller wil naam niet online waarschuwing'); ?>
    <div class="inputs">
        <?php echo $view->formTextarea('creatorprivatewarning', $creatorprivatewarning, array('rows' => '6', 'cols' => '60', 'class' => 'textinput')); ?>
        <p class="explanation">
            De waarschuwing die gegeven wordt wanneer de verteller niet wil worden weergegeven bij het verhaal.
        </p>
    </div>
</div>

<div class="field">
    <?php echo $view->formLabel('imagewarning', 'Auteursrecht tekst waarschuwing'); ?>
    <div class="inputs">
        <?php echo $view->formTextarea('imagewarning', $imagewarning, array('rows' => '6', 'cols' => '60', 'class' => 'textinput')); ?>
        <p class="explanation">
            De waarschuwing die gegeven wordt wanneer een plaatje niet online mag komen vanwege extreme inhoud of auteursrecht.
        </p>
    </div>
</div>

 <div class="field">
    <?php echo $view->formLabel('kloekelink', 'De link naar de Kloekenummer website'); ?>
    <div class="inputs">
        <?php echo $view->formText('kloekelink', $kloekelink, array('class' => 'textinput')); ?>
        <p class="explanation">
            Hier kan de kale link naar de site komen te staan waar kloekenummers kunnen worden ingevoerd om weer te geven op een kaartje.
        </p>
    </div>
</div>

 <div class="field">
    <?php echo $view->formLabel('motiflink', 'De link naar de Motif index website'); ?>
    <div class="inputs">
        <?php echo $view->formText('motiflink', $motiflink, array('class' => 'textinput')); ?>
        <p class="explanation">
            Hier kan de kale link naar de site komen te staan waar motieven kunnen worden bekeken.
        </p>
    </div>
</div>

 <div class="field">
    <?php echo $view->formLabel('mediumsearchablefields', 'ADVANCED: Medium zoekpaneel doorzoekbare velden'); ?>
    <div class="inputs">
        <?php echo $view->formText('mediumsearchablefields', $mediumsearchablefields, array('class' => 'textinput')); ?>
        <p class="explanation">
            Dit zijn de interne nummers van de zoekvelden die doorzocht kunnen worden vanaf het MEDIUM zoekpaneel
        </p>
    </div>
</div>


 <div class="field">
    <?php echo $view->formLabel('subcollectionswithtypes', 'ADVANCED: De Collection nummers waarin gezocht wordt bij de volksverhaaltype link'); ?>
    <div class="inputs">
        <?php echo $view->formText('subcollectionswithtypes', $subcollectionswithtypes, array('class' => 'textinput')); ?>
        <p class="explanation">
            Deze collecties worden doorzocht op het Volksverhaaltype dat bij een verhaal is aangegeven.
        </p>
    </div>
</div>


