<?php

use App\Models\Continent;
use App\Models\Country;
use App\Repositories\ContinentRepository;
use App\Repositories\CountryRepository;
use Illuminate\Database\Seeder;

class ContinentSeeder extends Seeder
{
    /** @var ContinentRepository */
    protected $continentRepository;

    /** @var CountryRepository */
    protected $countryRepository;

    public function __construct(ContinentRepository $continentRepository, CountryRepository $countryRepository)
    {
        $this->continentRepository = $continentRepository;
        $this->countryRepository = $countryRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Initialize Continents

        /** @var Continent $continentAfrica */
        $continentAfrica = $this->continentRepository->firstOrCreate([
            'name'         => 'Africa',
            'abbreviation' => 'AF'
        ]);

        /** @var Continent $continentAntartica */
        $continentAntartica = $this->continentRepository->firstOrCreate([
            'name'         => 'Antartica',
            'abbreviation' => 'AN'
        ]);

        /** @var Continent $continentAsia */
        $continentAsia = $this->continentRepository->firstOrCreate([
            'name'         => 'Asia',
            'abbreviation' => 'AS'
        ]);

        /** @var Continent $continentEurope */
        $continentEurope = $this->continentRepository->firstOrCreate([
            'name'         => 'Europe',
            'abbreviation' => 'EU'
        ]);

        /** @var Continent $continentNorthAmerica */
        $continentNorthAmerica = $this->continentRepository->firstOrCreate([
            'name'         => 'North America',
            'abbreviation' => 'NA'
        ]);

        /** @var Continent $continentOceania */
        $continentOceania = $this->continentRepository->firstOrCreate([
            'name'         => 'Oceania',
            'abbreviation' => 'OC'
        ]);

        /** @var Continent $continentSouthAmerica */
        $continentSouthAmerica = $this->continentRepository->firstOrCreate([
            'name'         => 'South America',
            'abbreviation' => 'SA'
        ]);

        // Countries

        /** @var Country $countryAfghanistan */
        $countryAfghanistan = $this->countryRepository->getByCode('AF');
        $this->countryRepository->setContinent($countryAfghanistan, $continentAsia);

        /** @var Country $countryAlandIslands */
        $countryAlandIslands = $this->countryRepository->getByCode('AX');
        $this->countryRepository->setContinent($countryAlandIslands, $continentEurope);

        /** @var Country $countryAlbania */
        $countryAlbania = $this->countryRepository->getByCode('AL');
        $this->countryRepository->setContinent($countryAlbania, $continentEurope);

        /** @var Country $countryAlgeria */
        $countryAlgeria = $this->countryRepository->getByCode('DZ');
        $this->countryRepository->setContinent($countryAlgeria, $continentAfrica);

        /** @var Country $countryAmericanSamoa */
        $countryAmericanSamoa = $this->countryRepository->getByCode('AS');
        $this->countryRepository->setContinent($countryAmericanSamoa, $continentOceania);

        /** @var Country $countryAndorra */
        $countryAndorra = $this->countryRepository->getByCode('AD');
        $this->countryRepository->setContinent($countryAndorra, $continentEurope);

        /** @var Country $countryAngola */
        $countryAngola = $this->countryRepository->getByCode('AO');
        $this->countryRepository->setContinent($countryAngola, $continentAfrica);

        /** @var Country $countryAnguilla */
        $countryAnguilla = $this->countryRepository->getByCode('AI');
        $this->countryRepository->setContinent($countryAnguilla, $continentNorthAmerica);

        /** @var Country $countryAntarctica */
        $countryAntarctica = $this->countryRepository->getByCode('AQ');
        $this->countryRepository->setContinent($countryAntarctica, $continentAntartica);

        /** @var Country $countryAntiguaAndBarbuda */
        $countryAntiguaAndBarbuda = $this->countryRepository->getByCode('AG');
        $this->countryRepository->setContinent($countryAntiguaAndBarbuda, $continentNorthAmerica);

        /** @var Country $countryArgentina */
        $countryArgentina = $this->countryRepository->getByCode('AR');
        $this->countryRepository->setContinent($countryArgentina, $continentSouthAmerica);

        /** @var Country $countryArmenia */
        $countryArmenia = $this->countryRepository->getByCode('AM');
        $this->countryRepository->setContinent($countryArmenia, $continentAsia);

        /** @var Country $countryAruba */
        $countryAruba = $this->countryRepository->getByCode('AW');
        $this->countryRepository->setContinent($countryAruba, $continentNorthAmerica);

        /** @var Country $countryAustralia */
        $countryAustralia = $this->countryRepository->getByCode('AU');
        $this->countryRepository->setContinent($countryAustralia, $continentOceania);

        /** @var Country $countryAustria */
        $countryAustria = $this->countryRepository->getByCode('AT');
        $this->countryRepository->setContinent($countryAustria, $continentEurope);

        /** @var Country $countryAzerbaijan */
        $countryAzerbaijan = $this->countryRepository->getByCode('AZ');
        $this->countryRepository->setContinent($countryAzerbaijan, $continentEurope);

        /** @var Country $countryBahamas */
        $countryBahamas = $this->countryRepository->getByCode('BS');
        $this->countryRepository->setContinent($countryBahamas, $continentNorthAmerica);

        /** @var Country $countryBahrain */
        $countryBahrain = $this->countryRepository->getByCode('BH');
        $this->countryRepository->setContinent($countryBahrain, $continentAsia);

        /** @var Country $countryBangladesh */
        $countryBangladesh = $this->countryRepository->getByCode('BD');
        $this->countryRepository->setContinent($countryBangladesh, $continentAsia);

        /** @var Country $countryBarbados */
        $countryBarbados = $this->countryRepository->getByCode('BB');
        $this->countryRepository->setContinent($countryBarbados, $continentNorthAmerica);

        /** @var Country $countryBelarus */
        $countryBelarus = $this->countryRepository->getByCode('BY');
        $this->countryRepository->setContinent($countryBelarus, $continentEurope);

        /** @var Country $countryBelgium */
        $countryBelgium = $this->countryRepository->getByCode('BE');
        $this->countryRepository->setContinent($countryBelgium, $continentEurope);

        /** @var Country $countryBelize */
        $countryBelize = $this->countryRepository->getByCode('BZ');
        $this->countryRepository->setContinent($countryBelize, $continentNorthAmerica);

        /** @var Country $countryBenin */
        $countryBenin = $this->countryRepository->getByCode('BJ');
        $this->countryRepository->setContinent($countryBenin, $continentAfrica);

        /** @var Country $countryBermuda */
        $countryBermuda = $this->countryRepository->getByCode('BM');
        $this->countryRepository->setContinent($countryBermuda, $continentNorthAmerica);

        /** @var Country $countryBhutan */
        $countryBhutan = $this->countryRepository->getByCode('BT');
        $this->countryRepository->setContinent($countryBhutan, $continentAsia);

        /** @var Country $countryBolivia */
        $countryBolivia = $this->countryRepository->getByCode('BO');
        $this->countryRepository->setContinent($countryBolivia, $continentSouthAmerica);

        /** @var Country $countryBosniaAndHerzegovina */
        $countryBosniaAndHerzegovina = $this->countryRepository->getByCode('BA');
        $this->countryRepository->setContinent($countryBosniaAndHerzegovina, $continentEurope);

        /** @var Country $countryBotswana */
        $countryBotswana = $this->countryRepository->getByCode('BW');
        $this->countryRepository->setContinent($countryBotswana, $continentAfrica);

        /** @var Country $countryBouvetIsland */
        $countryBouvetIsland = $this->countryRepository->getByCode('BV');
        $this->countryRepository->setContinent($countryBouvetIsland, $continentAntartica);

        /** @var Country $countryBrazil */
        $countryBrazil = $this->countryRepository->getByCode('BR');
        $this->countryRepository->setContinent($countryBrazil, $continentSouthAmerica);

        /** @var Country $countryBritishIndianOceanTerritory */
        $countryBritishIndianOceanTerritory = $this->countryRepository->getByCode('IO');
        $this->countryRepository->setContinent($countryBritishIndianOceanTerritory, $continentAsia);

        /** @var Country $countryBruneiDarussalam */
        $countryBruneiDarussalam = $this->countryRepository->getByCode('BN');
        $this->countryRepository->setContinent($countryBruneiDarussalam, $continentAsia);

        /** @var Country $countryBulgaria */
        $countryBulgaria = $this->countryRepository->getByCode('BG');
        $this->countryRepository->setContinent($countryBulgaria, $continentEurope);

        /** @var Country $countryBurkinaFaso */
        $countryBurkinaFaso = $this->countryRepository->getByCode('BF');
        $this->countryRepository->setContinent($countryBurkinaFaso, $continentAfrica);

        /** @var Country $countryBurundi */
        $countryBurundi = $this->countryRepository->getByCode('BI');
        $this->countryRepository->setContinent($countryBurundi, $continentAfrica);

        /** @var Country $countryCambodia */
        $countryCambodia = $this->countryRepository->getByCode('KH');
        $this->countryRepository->setContinent($countryCambodia, $continentAfrica);

        /** @var Country $countryCameroon */
        $countryCameroon = $this->countryRepository->getByCode('CM');
        $this->countryRepository->setContinent($countryCameroon, $continentAfrica);

        /** @var Country $countryCanada */
        $countryCanada = $this->countryRepository->getByCode('CA');
        $this->countryRepository->setContinent($countryCanada, $continentNorthAmerica);

        /** @var Country $countryCapeVerde */
        $countryCapeVerde = $this->countryRepository->getByCode('CV');
        $this->countryRepository->setContinent($countryCapeVerde, $continentAfrica);

        /** @var Country $countryCaymanIslands */
        $countryCaymanIslands = $this->countryRepository->getByCode('KY');
        $this->countryRepository->setContinent($countryCaymanIslands, $continentNorthAmerica);

        /** @var Country $countryCentralAfricanRepublic */
        $countryCentralAfricanRepublic = $this->countryRepository->getByCode('CF');
        $this->countryRepository->setContinent($countryCentralAfricanRepublic, $continentAfrica);

        /** @var Country $countryChad */
        $countryChad = $this->countryRepository->getByCode('TD');
        $this->countryRepository->setContinent($countryChad, $continentAfrica);

        /** @var Country $countryChile */
        $countryChile = $this->countryRepository->getByCode('CL');
        $this->countryRepository->setContinent($countryChile, $continentSouthAmerica);

        /** @var Country $countryChina */
        $countryChina = $this->countryRepository->getByCode('CN');
        $this->countryRepository->setContinent($countryChina, $continentAsia);

        /** @var Country $countryChristmasIsland */
        $countryChristmasIsland = $this->countryRepository->getByCode('CX');
        $this->countryRepository->setContinent($countryChristmasIsland, $continentAsia);

        /** @var Country $countryCocosKeelingIslands */
        $countryCocosKeelingIslands = $this->countryRepository->getByCode('CC');
        $this->countryRepository->setContinent($countryCocosKeelingIslands, $continentOceania);

        /** @var Country $countryColombia */
        $countryColombia = $this->countryRepository->getByCode('CO');
        $this->countryRepository->setContinent($countryColombia, $continentSouthAmerica);

        /** @var Country $countryComoros */
        $countryComoros = $this->countryRepository->getByCode('KM');
        $this->countryRepository->setContinent($countryComoros, $continentAfrica);

        /** @var Country $countryCongo */
        $countryCongo = $this->countryRepository->getByCode('CG');
        $this->countryRepository->setContinent($countryCongo, $continentAfrica);

        /** @var Country $countryCongoTheDemocraticRepublicOfThe */
        $countryCongoTheDemocraticRepublicOfThe = $this->countryRepository->getByCode('CD');
        $this->countryRepository->setContinent($countryCongoTheDemocraticRepublicOfThe, $continentAfrica);

        /** @var Country $countryCookIslands */
        $countryCookIslands = $this->countryRepository->getByCode('CK');
        $this->countryRepository->setContinent($countryCookIslands, $continentOceania);

        /** @var Country $countryCostaRica */
        $countryCostaRica = $this->countryRepository->getByCode('CR');
        $this->countryRepository->setContinent($countryCostaRica, $continentNorthAmerica);

        /** @var Country $countryCoteDIvoire */
        $countryCoteDIvoire = $this->countryRepository->getByCode('CI');
        $this->countryRepository->setContinent($countryCoteDIvoire, $continentAfrica);

        /** @var Country $countryCroatia */
        $countryCroatia = $this->countryRepository->getByCode('HR');
        $this->countryRepository->setContinent($countryCroatia, $continentEurope);

        /** @var Country $countryCuba */
        $countryCuba = $this->countryRepository->getByCode('CU');
        $this->countryRepository->setContinent($countryCuba, $continentNorthAmerica);

        /** @var Country $countryCyprus */
        $countryCyprus = $this->countryRepository->getByCode('CY');
        $this->countryRepository->setContinent($countryCyprus, $continentAsia);

        /** @var Country $countryCyprus */
        $countryCyprus = $this->countryRepository->getByCode('CY');
        $this->countryRepository->setContinent($countryCyprus, $continentAsia);

        /** @var Country $countryCzechRepublic */
        $countryCzechRepublic = $this->countryRepository->getByCode('CZ');
        $this->countryRepository->setContinent($countryCzechRepublic, $continentEurope);

        /** @var Country $countryDenmark */
        $countryDenmark = $this->countryRepository->getByCode('DK');
        $this->countryRepository->setContinent($countryDenmark, $continentEurope);

        /** @var Country $countryDjibouti */
        $countryDjibouti = $this->countryRepository->getByCode('DJ');
        $this->countryRepository->setContinent($countryDjibouti, $continentAfrica);

        /** @var Country $countryDominica */
        $countryDominica = $this->countryRepository->getByCode('DM');
        $this->countryRepository->setContinent($countryDominica, $continentNorthAmerica);

        /** @var Country $countryDominicanRepublic */
        $countryDominicanRepublic = $this->countryRepository->getByCode('DO');
        $this->countryRepository->setContinent($countryDominicanRepublic, $continentNorthAmerica);

        /** @var Country $countryEcuador */
        $countryEcuador = $this->countryRepository->getByCode('EC');
        $this->countryRepository->setContinent($countryEcuador, $continentSouthAmerica);

        /** @var Country $countryEgypt */
        $countryEgypt = $this->countryRepository->getByCode('EG');
        $this->countryRepository->setContinent($countryEgypt, $continentAfrica);

        /** @var Country $countryElSalvador */
        $countryElSalvador = $this->countryRepository->getByCode('SV');
        $this->countryRepository->setContinent($countryElSalvador, $continentNorthAmerica);

        /** @var Country $countryEquatorialGuinea */
        $countryEquatorialGuinea = $this->countryRepository->getByCode('GQ');
        $this->countryRepository->setContinent($countryEquatorialGuinea, $continentAfrica);

        /** @var Country $countryEritrea */
        $countryEritrea = $this->countryRepository->getByCode('ER');
        $this->countryRepository->setContinent($countryEritrea, $continentAfrica);

        /** @var Country $countryEstonia */
        $countryEstonia = $this->countryRepository->getByCode('EE');
        $this->countryRepository->setContinent($countryEstonia, $continentEurope);

        /** @var Country $countryEthiopia */
        $countryEthiopia = $this->countryRepository->getByCode('ET');
        $this->countryRepository->setContinent($countryEthiopia, $continentAfrica);

        /** @var Country $countryFalklandIslandsMalvinas */
        $countryFalklandIslandsMalvinas = $this->countryRepository->getByCode('FK');
        $this->countryRepository->setContinent($countryFalklandIslandsMalvinas, $continentSouthAmerica);

        /** @var Country $countryFaroeIslands */
        $countryFaroeIslands = $this->countryRepository->getByCode('FO');
        $this->countryRepository->setContinent($countryFaroeIslands, $continentEurope);

        /** @var Country $countryFiji */
        $countryFiji = $this->countryRepository->getByCode('FJ');
        $this->countryRepository->setContinent($countryFiji, $continentOceania);

        /** @var Country $countryFinland */
        $countryFinland = $this->countryRepository->getByCode('FI');
        $this->countryRepository->setContinent($countryFinland, $continentEurope);

        /** @var Country $countryFrance */
        $countryFrance = $this->countryRepository->getByCode('FR');
        $this->countryRepository->setContinent($countryFrance, $continentEurope);

        /** @var Country $countryFrenchGuiana */
        $countryFrenchGuiana = $this->countryRepository->getByCode('GF');
        $this->countryRepository->setContinent($countryFrenchGuiana, $continentSouthAmerica);

        /** @var Country $countryFrenchPolynesia */
        $countryFrenchPolynesia = $this->countryRepository->getByCode('PF');
        $this->countryRepository->setContinent($countryFrenchPolynesia, $continentOceania);

        /** @var Country $countryFrenchSouthernTerritories */
        $countryFrenchSouthernTerritories = $this->countryRepository->getByCode('TF');
        $this->countryRepository->setContinent($countryFrenchSouthernTerritories, $continentAntartica);

        /** @var Country $countryGabon */
        $countryGabon = $this->countryRepository->getByCode('GA');
        $this->countryRepository->setContinent($countryGabon, $continentAfrica);

        /** @var Country $countryGambia */
        $countryGambia = $this->countryRepository->getByCode('GM');
        $this->countryRepository->setContinent($countryGambia, $continentAfrica);

        /** @var Country $countryGeorgia */
        $countryGeorgia = $this->countryRepository->getByCode('GE');
        $this->countryRepository->setContinent($countryGeorgia, $continentAsia);

        /** @var Country $countryGermany */
        $countryGermany = $this->countryRepository->getByCode('DE');
        $this->countryRepository->setContinent($countryGermany, $continentEurope);

        /** @var Country $countryGhana */
        $countryGhana = $this->countryRepository->getByCode('GH');
        $this->countryRepository->setContinent($countryGhana, $continentAfrica);

        /** @var Country $countryGibraltar */
        $countryGibraltar = $this->countryRepository->getByCode('GI');
        $this->countryRepository->setContinent($countryGibraltar, $continentEurope);

        /** @var Country $countryGreece */
        $countryGreece = $this->countryRepository->getByCode('GR');
        $this->countryRepository->setContinent($countryGreece, $continentEurope);

        /** @var Country $countryGreenland */
        $countryGreenland = $this->countryRepository->getByCode('GL');
        $this->countryRepository->setContinent($countryGreenland, $continentNorthAmerica);

        /** @var Country $countryGrenada */
        $countryGrenada = $this->countryRepository->getByCode('GD');
        $this->countryRepository->setContinent($countryGrenada, $continentNorthAmerica);

        /** @var Country $countryGuadeloupe */
        $countryGuadeloupe = $this->countryRepository->getByCode('GP');
        $this->countryRepository->setContinent($countryGuadeloupe, $continentNorthAmerica);

        /** @var Country $countryGuam */
        $countryGuam = $this->countryRepository->getByCode('GU');
        $this->countryRepository->setContinent($countryGuam, $continentOceania);

        /** @var Country $countryGuatemala */
        $countryGuatemala = $this->countryRepository->getByCode('GT');
        $this->countryRepository->setContinent($countryGuatemala, $continentNorthAmerica);

        /** @var Country $countryGuernsey */
        $countryGuernsey = $this->countryRepository->getByCode('GG');
        $this->countryRepository->setContinent($countryGuernsey, $continentEurope);

        /** @var Country $countryGuinea */
        $countryGuinea = $this->countryRepository->getByCode('GN');
        $this->countryRepository->setContinent($countryGuinea, $continentAfrica);

        /** @var Country $countryGuineaBissau */
        $countryGuineaBissau = $this->countryRepository->getByCode('GW');
        $this->countryRepository->setContinent($countryGuineaBissau, $continentAfrica);

        /** @var Country $countryGuyana */
        $countryGuyana = $this->countryRepository->getByCode('GY');
        $this->countryRepository->setContinent($countryGuyana, $continentSouthAmerica);

        /** @var Country $countryHaiti */
        $countryHaiti = $this->countryRepository->getByCode('HT');
        $this->countryRepository->setContinent($countryHaiti, $continentNorthAmerica);

        /** @var Country $countryHeardIslandAndMcdonaldIslands */
        $countryHeardIslandAndMcdonaldIslands = $this->countryRepository->getByCode('HM');
        $this->countryRepository->setContinent($countryHeardIslandAndMcdonaldIslands, $continentAntartica);

        /** @var Country $countryHolySeeVaticanCityState */
        $countryHolySeeVaticanCityState = $this->countryRepository->getByCode('VA');
        $this->countryRepository->setContinent($countryHolySeeVaticanCityState, $continentEurope);

        /** @var Country $countryHonduras */
        $countryHonduras = $this->countryRepository->getByCode('HN');
        $this->countryRepository->setContinent($countryHonduras, $continentNorthAmerica);

        /** @var Country $countryHongKong */
        $countryHongKong = $this->countryRepository->getByCode('HK');
        $this->countryRepository->setContinent($countryHongKong, $continentAsia);

        /** @var Country $countryHungary */
        $countryHungary = $this->countryRepository->getByCode('HU');
        $this->countryRepository->setContinent($countryHungary, $continentEurope);

        /** @var Country $countryIceland */
        $countryIceland = $this->countryRepository->getByCode('IS');
        $this->countryRepository->setContinent($countryIceland, $continentEurope);

        /** @var Country $countryIndia */
        $countryIndia = $this->countryRepository->getByCode('IN');
        $this->countryRepository->setContinent($countryIndia, $continentAsia);

        /** @var Country $countryIndonesia */
        $countryIndonesia = $this->countryRepository->getByCode('ID');
        $this->countryRepository->setContinent($countryIndonesia, $continentAsia);

        /** @var Country $countryIranIslamicRepublicOf */
        $countryIranIslamicRepublicOf = $this->countryRepository->getByCode('IR');
        $this->countryRepository->setContinent($countryIranIslamicRepublicOf, $continentAsia);

        /** @var Country $countryIraq */
        $countryIraq = $this->countryRepository->getByCode('IQ');
        $this->countryRepository->setContinent($countryIraq, $continentAsia);

        /** @var Country $countryIreland */
        $countryIreland = $this->countryRepository->getByCode('IE');
        $this->countryRepository->setContinent($countryIreland, $continentEurope);

        /** @var Country $countryIsleOfMan */
        $countryIsleOfMan = $this->countryRepository->getByCode('IM');
        $this->countryRepository->setContinent($countryIsleOfMan, $continentEurope);

        /** @var Country $countryIsrael */
        $countryIsrael = $this->countryRepository->getByCode('IL');
        $this->countryRepository->setContinent($countryIsrael, $continentAsia);

        /** @var Country $countryItaly */
        $countryItaly = $this->countryRepository->getByCode('IT');
        $this->countryRepository->setContinent($countryItaly, $continentEurope);

        /** @var Country $countryJamaica */
        $countryJamaica = $this->countryRepository->getByCode('JM');
        $this->countryRepository->setContinent($countryJamaica, $continentNorthAmerica);

        /** @var Country $countryJapan */
        $countryJapan = $this->countryRepository->getByCode('JP');
        $this->countryRepository->setContinent($countryJapan, $continentAsia);

        /** @var Country $countryJersey */
        $countryJersey = $this->countryRepository->getByCode('JE');
        $this->countryRepository->setContinent($countryJersey, $continentEurope);

        /** @var Country $countryJordan */
        $countryJordan = $this->countryRepository->getByCode('JO');
        $this->countryRepository->setContinent($countryJordan, $continentAsia);

        /** @var Country $countryKazakhstan */
        $countryKazakhstan = $this->countryRepository->getByCode('KZ');
        $this->countryRepository->setContinent($countryKazakhstan, $continentAsia);

        /** @var Country $countryKenya */
        $countryKenya = $this->countryRepository->getByCode('KE');
        $this->countryRepository->setContinent($countryKenya, $continentAfrica);

        /** @var Country $countryKiribati */
        $countryKiribati = $this->countryRepository->getByCode('KI');
        $this->countryRepository->setContinent($countryKiribati, $continentOceania);

        /** @var Country $countryKoreaDemocraticPeopleRepublicOf */
        $countryKoreaDemocraticPeopleRepublicOf = $this->countryRepository->getByCode('KP');
        $this->countryRepository->setContinent($countryKoreaDemocraticPeopleRepublicOf, $continentAsia);

        /** @var Country $countryKoreaRepublicOf */
        $countryKoreaRepublicOf = $this->countryRepository->getByCode('KR');
        $this->countryRepository->setContinent($countryKoreaRepublicOf, $continentAsia);

        /** @var Country $countryKuwait */
        $countryKuwait = $this->countryRepository->getByCode('KW');
        $this->countryRepository->setContinent($countryKuwait, $continentAsia);

        /** @var Country $countryKyrgyzstan */
        $countryKyrgyzstan = $this->countryRepository->getByCode('KG');
        $this->countryRepository->setContinent($countryKyrgyzstan, $continentAsia);

        /** @var Country $countryLaoPeopleDemocraticRepublic */
        $countryLaoPeopleDemocraticRepublic = $this->countryRepository->getByCode('LA');
        $this->countryRepository->setContinent($countryLaoPeopleDemocraticRepublic, $continentAsia);

        /** @var Country $countryLatvia */
        $countryLatvia = $this->countryRepository->getByCode('LV');
        $this->countryRepository->setContinent($countryLatvia, $continentEurope);

        /** @var Country $countryLebanon */
        $countryLebanon = $this->countryRepository->getByCode('LB');
        $this->countryRepository->setContinent($countryLebanon, $continentAsia);

        /** @var Country $countryLesotho */
        $countryLesotho = $this->countryRepository->getByCode('LS');
        $this->countryRepository->setContinent($countryLesotho, $continentAfrica);

        /** @var Country $countryLiberia */
        $countryLiberia = $this->countryRepository->getByCode('LR');
        $this->countryRepository->setContinent($countryLiberia, $continentAfrica);

        /** @var Country $countryLibyanArabJamahiriya */
        $countryLibyanArabJamahiriya = $this->countryRepository->getByCode('LY');
        $this->countryRepository->setContinent($countryLibyanArabJamahiriya, $continentAfrica);

        /** @var Country $countryLiechtenstein */
        $countryLiechtenstein = $this->countryRepository->getByCode('LI');
        $this->countryRepository->setContinent($countryLiechtenstein, $continentEurope);

        /** @var Country $countryLithuania */
        $countryLithuania = $this->countryRepository->getByCode('LT');
        $this->countryRepository->setContinent($countryLithuania, $continentEurope);

        /** @var Country $countryLuxembourg */
        $countryLuxembourg = $this->countryRepository->getByCode('LU');
        $this->countryRepository->setContinent($countryLuxembourg, $continentEurope);

        /** @var Country $countryMacao */
        $countryMacao = $this->countryRepository->getByCode('MO');
        $this->countryRepository->setContinent($countryMacao, $continentEurope);

        /** @var Country $countryMacedoniaTheFormerYugoslavRepublicOf */
        $countryMacedoniaTheFormerYugoslavRepublicOf = $this->countryRepository->getByCode('MK');
        $this->countryRepository->setContinent($countryMacedoniaTheFormerYugoslavRepublicOf, $continentEurope);

        /** @var Country $countryMadagascar */
        $countryMadagascar = $this->countryRepository->getByCode('MG');
        $this->countryRepository->setContinent($countryMadagascar, $continentAfrica);

        /** @var Country $countryMalawi */
        $countryMalawi = $this->countryRepository->getByCode('MW');
        $this->countryRepository->setContinent($countryMalawi, $continentAfrica);

        /** @var Country $countryMalaysia */
        $countryMalaysia = $this->countryRepository->getByCode('MY');
        $this->countryRepository->setContinent($countryMalaysia, $continentAsia);

        /** @var Country $countryMaldives */
        $countryMaldives = $this->countryRepository->getByCode('MV');
        $this->countryRepository->setContinent($countryMaldives, $continentAsia);

        /** @var Country $countryMali */
        $countryMali = $this->countryRepository->getByCode('ML');
        $this->countryRepository->setContinent($countryMali, $continentAfrica);

        /** @var Country $countryMalta */
        $countryMalta = $this->countryRepository->getByCode('MT');
        $this->countryRepository->setContinent($countryMalta, $continentEurope);

        /** @var Country $countryMarshallIslands */
        $countryMarshallIslands = $this->countryRepository->getByCode('MH');
        $this->countryRepository->setContinent($countryMarshallIslands, $continentOceania);

        /** @var Country $countryMartinique */
        $countryMartinique = $this->countryRepository->getByCode('MQ');
        $this->countryRepository->setContinent($countryMartinique, $continentNorthAmerica);

        /** @var Country $countryMauritania */
        $countryMauritania = $this->countryRepository->getByCode('MR');
        $this->countryRepository->setContinent($countryMauritania, $continentAfrica);

        /** @var Country $countryMauritius */
        $countryMauritius = $this->countryRepository->getByCode('MU');
        $this->countryRepository->setContinent($countryMauritius, $continentAfrica);

        /** @var Country $countryMayotte */
        $countryMayotte = $this->countryRepository->getByCode('YT');
        $this->countryRepository->setContinent($countryMayotte, $continentAfrica);

        /** @var Country $countryMexico */
        $countryMexico = $this->countryRepository->getByCode('MX');
        $this->countryRepository->setContinent($countryMexico, $continentNorthAmerica);

        /** @var Country $countryMicronesiaFederatedStatesOf */
        $countryMicronesiaFederatedStatesOf = $this->countryRepository->getByCode('FM');
        $this->countryRepository->setContinent($countryMicronesiaFederatedStatesOf, $continentOceania);

        /** @var Country $countryMoldovaRepublicOf */
        $countryMoldovaRepublicOf = $this->countryRepository->getByCode('MD');
        $this->countryRepository->setContinent($countryMoldovaRepublicOf, $continentEurope);

        /** @var Country $countryMonaco */
        $countryMonaco = $this->countryRepository->getByCode('MC');
        $this->countryRepository->setContinent($countryMonaco, $continentEurope);

        /** @var Country $countryMongolia */
        $countryMongolia = $this->countryRepository->getByCode('MN');
        $this->countryRepository->setContinent($countryMongolia, $continentAsia);

        /** @var Country $countryMontserrat */
        $countryMontserrat = $this->countryRepository->getByCode('MS');
        $this->countryRepository->setContinent($countryMontserrat, $continentNorthAmerica);

        /** @var Country $countryMorocco */
        $countryMorocco = $this->countryRepository->getByCode('MA');
        $this->countryRepository->setContinent($countryMorocco, $continentAfrica);

        /** @var Country $countryMozambique */
        $countryMozambique = $this->countryRepository->getByCode('MZ');
        $this->countryRepository->setContinent($countryMozambique, $continentAfrica);

        /** @var Country $countryMyanmar */
        $countryMyanmar = $this->countryRepository->getByCode('MM');
        $this->countryRepository->setContinent($countryMyanmar, $continentAsia);

        /** @var Country $countryNamibia */
        $countryNamibia = $this->countryRepository->getByCode('NA');
        $this->countryRepository->setContinent($countryNamibia, $continentAfrica);

        /** @var Country $countryNauru */
        $countryNauru = $this->countryRepository->getByCode('NR');
        $this->countryRepository->setContinent($countryNauru, $continentOceania);

        /** @var Country $countryNepal */
        $countryNepal = $this->countryRepository->getByCode('NP');
        $this->countryRepository->setContinent($countryNepal, $continentAsia);

        /** @var Country $countryNetherlands */
        $countryNetherlands = $this->countryRepository->getByCode('NL');
        $this->countryRepository->setContinent($countryNetherlands, $continentEurope);

        /** @var Country $countryNetherlandsAntilles */
        $countryNetherlandsAntilles = $this->countryRepository->getByCode('AN');
        $this->countryRepository->setContinent($countryNetherlandsAntilles, $continentNorthAmerica);

        /** @var Country $countryNewCaledonia */
        $countryNewCaledonia = $this->countryRepository->getByCode('NC');
        $this->countryRepository->setContinent($countryNewCaledonia, $continentOceania);

        /** @var Country $countryNewZealand */
        $countryNewZealand = $this->countryRepository->getByCode('NZ');
        $this->countryRepository->setContinent($countryNewZealand, $continentOceania);

        /** @var Country $countryNicaragua */
        $countryNicaragua = $this->countryRepository->getByCode('NI');
        $this->countryRepository->setContinent($countryNicaragua, $continentNorthAmerica);

        /** @var Country $countryNiger */
        $countryNiger = $this->countryRepository->getByCode('NE');
        $this->countryRepository->setContinent($countryNiger, $continentAfrica);

        /** @var Country $countryNigeria */
        $countryNigeria = $this->countryRepository->getByCode('NG');
        $this->countryRepository->setContinent($countryNigeria, $continentAfrica);

        /** @var Country $countryNiue */
        $countryNiue = $this->countryRepository->getByCode('NU');
        $this->countryRepository->setContinent($countryNiue, $continentOceania);

        /** @var Country $countryNorfolkIsland */
        $countryNorfolkIsland = $this->countryRepository->getByCode('NF');
        $this->countryRepository->setContinent($countryNorfolkIsland, $continentOceania);

        /** @var Country $countryNorthernMarianaIslands */
        $countryNorthernMarianaIslands = $this->countryRepository->getByCode('MP');
        $this->countryRepository->setContinent($countryNorthernMarianaIslands, $continentOceania);

        /** @var Country $countryNorway */
        $countryNorway = $this->countryRepository->getByCode('NO');
        $this->countryRepository->setContinent($countryNorway, $continentEurope);

        /** @var Country $countryOman */
        $countryOman = $this->countryRepository->getByCode('OM');
        $this->countryRepository->setContinent($countryOman, $continentAsia);

        /** @var Country $countryPakistan */
        $countryPakistan = $this->countryRepository->getByCode('PK');
        $this->countryRepository->setContinent($countryPakistan, $continentAsia);

        /** @var Country $countryPalau */
        $countryPalau = $this->countryRepository->getByCode('PW');
        $this->countryRepository->setContinent($countryPalau, $continentOceania);

        /** @var Country $countryPalestinianTerritoryOccupied */
        $countryPalestinianTerritoryOccupied = $this->countryRepository->getByCode('PS');
        $this->countryRepository->setContinent($countryPalestinianTerritoryOccupied, $continentAsia);

        /** @var Country $countryPanama */
        $countryPanama = $this->countryRepository->getByCode('PA');
        $this->countryRepository->setContinent($countryPanama, $continentNorthAmerica);

        /** @var Country $countryPapuaNewGuinea */
        $countryPapuaNewGuinea = $this->countryRepository->getByCode('PG');
        $this->countryRepository->setContinent($countryPapuaNewGuinea, $continentOceania);

        /** @var Country $countryParaguay */
        $countryParaguay = $this->countryRepository->getByCode('PY');
        $this->countryRepository->setContinent($countryParaguay, $continentSouthAmerica);

        /** @var Country $countryPeru */
        $countryPeru = $this->countryRepository->getByCode('PE');
        $this->countryRepository->setContinent($countryPeru, $continentSouthAmerica);

        /** @var Country $countryPhilippines */
        $countryPhilippines = $this->countryRepository->getByCode('PH');
        $this->countryRepository->setContinent($countryPhilippines, $continentAsia);

        /** @var Country $countryPitcairn */
        $countryPitcairn = $this->countryRepository->getByCode('PN');
        $this->countryRepository->setContinent($countryPitcairn, $continentOceania);

        /** @var Country $countryPoland */
        $countryPoland = $this->countryRepository->getByCode('PL');
        $this->countryRepository->setContinent($countryPoland, $continentEurope);

        /** @var Country $countryPortugal */
        $countryPortugal = $this->countryRepository->getByCode('PT');
        $this->countryRepository->setContinent($countryPortugal, $continentEurope);

        /** @var Country $countryPuertoRico */
        $countryPuertoRico = $this->countryRepository->getByCode('PR');
        $this->countryRepository->setContinent($countryPuertoRico, $continentNorthAmerica);

        /** @var Country $countryQatar */
        $countryQatar = $this->countryRepository->getByCode('QA');
        $this->countryRepository->setContinent($countryQatar, $continentAsia);

        /** @var Country $countryReunion */
        $countryReunion = $this->countryRepository->getByCode('RE');
        $this->countryRepository->setContinent($countryReunion, $continentAfrica);

        /** @var Country $countryRomania */
        $countryRomania = $this->countryRepository->getByCode('RO');
        $this->countryRepository->setContinent($countryRomania, $continentEurope);

        /** @var Country $countryRussianFederation */
        $countryRussianFederation = $this->countryRepository->getByCode('RU');
        $this->countryRepository->setContinent($countryRussianFederation, $continentEurope);

        /** @var Country $countryRWANDA */
        $countryRWANDA = $this->countryRepository->getByCode('RW');
        $this->countryRepository->setContinent($countryRWANDA, $continentAfrica);

        /** @var Country $countrySaintHelena */
        $countrySaintHelena = $this->countryRepository->getByCode('SH');
        $this->countryRepository->setContinent($countrySaintHelena, $continentAfrica);

        /** @var Country $countrySaintKittsAndNevis */
        $countrySaintKittsAndNevis = $this->countryRepository->getByCode('KN');
        $this->countryRepository->setContinent($countrySaintKittsAndNevis, $continentNorthAmerica);

        /** @var Country $countrySaintLucia */
        $countrySaintLucia = $this->countryRepository->getByCode('LC');
        $this->countryRepository->setContinent($countrySaintLucia, $continentNorthAmerica);

        /** @var Country $countrySaintPierreAndMiquelon */
        $countrySaintPierreAndMiquelon = $this->countryRepository->getByCode('PM');
        $this->countryRepository->setContinent($countrySaintPierreAndMiquelon, $continentNorthAmerica);

        /** @var Country $countrySaintVincentAndTheGrenadines */
        $countrySaintVincentAndTheGrenadines = $this->countryRepository->getByCode('VC');
        $this->countryRepository->setContinent($countrySaintVincentAndTheGrenadines, $continentNorthAmerica);

        /** @var Country $countrySamoa */
        $countrySamoa = $this->countryRepository->getByCode('WS');
        $this->countryRepository->setContinent($countrySamoa, $continentOceania);

        /** @var Country $countrySanMarino */
        $countrySanMarino = $this->countryRepository->getByCode('SM');
        $this->countryRepository->setContinent($countrySanMarino, $continentEurope);

        /** @var Country $countrySaoTomeAndPrincipe */
        $countrySaoTomeAndPrincipe = $this->countryRepository->getByCode('ST');
        $this->countryRepository->setContinent($countrySaoTomeAndPrincipe, $continentAfrica);

        /** @var Country $countrySaudiArabia */
        $countrySaudiArabia = $this->countryRepository->getByCode('SA');
        $this->countryRepository->setContinent($countrySaudiArabia, $continentAsia);

        /** @var Country $countrySenegal */
        $countrySenegal = $this->countryRepository->getByCode('SN');
        $this->countryRepository->setContinent($countrySenegal, $continentAfrica);

        /** @var Country $countrySerbiaAndMontenegro */
        $countrySerbiaAndMontenegro = $this->countryRepository->getByCode('CS');
        $this->countryRepository->setContinent($countrySerbiaAndMontenegro, $continentEurope);

        /** @var Country $countrySeychelles */
        $countrySeychelles = $this->countryRepository->getByCode('SC');
        $this->countryRepository->setContinent($countrySeychelles, $continentAfrica);

        /** @var Country $countrySingapore */
        $countrySingapore = $this->countryRepository->getByCode('SG');
        $this->countryRepository->setContinent($countrySingapore, $continentAsia);

        /** @var Country $countrySlovakia */
        $countrySlovakia = $this->countryRepository->getByCode('SK');
        $this->countryRepository->setContinent($countrySlovakia, $continentEurope);

        /** @var Country $countrySlovenia */
        $countrySlovenia = $this->countryRepository->getByCode('SI');
        $this->countryRepository->setContinent($countrySlovenia, $continentEurope);

        /** @var Country $countrySolomonIslands */
        $countrySolomonIslands = $this->countryRepository->getByCode('SB');
        $this->countryRepository->setContinent($countrySolomonIslands, $continentOceania);

        /** @var Country $countrySomalia */
        $countrySomalia = $this->countryRepository->getByCode('SO');
        $this->countryRepository->setContinent($countrySomalia, $continentAfrica);

        /** @var Country $countrySouthAfrica */
        $countrySouthAfrica = $this->countryRepository->getByCode('ZA');
        $this->countryRepository->setContinent($countrySouthAfrica, $continentAfrica);

        /** @var Country $countrySouthGeorgiaAndTheSouthSandwichIslands */
        $countrySouthGeorgiaAndTheSouthSandwichIslands = $this->countryRepository->getByCode('GS');
        $this->countryRepository->setContinent($countrySouthGeorgiaAndTheSouthSandwichIslands, $continentAntartica);

        /** @var Country $countrySpain */
        $countrySpain = $this->countryRepository->getByCode('ES');
        $this->countryRepository->setContinent($countrySpain, $continentEurope);

        /** @var Country $countrySriLanka */
        $countrySriLanka = $this->countryRepository->getByCode('LK');
        $this->countryRepository->setContinent($countrySriLanka, $continentAsia);

        /** @var Country $countrySudan */
        $countrySudan = $this->countryRepository->getByCode('SD');
        $this->countryRepository->setContinent($countrySudan, $continentAfrica);

        /** @var Country $countrySuriname */
        $countrySuriname = $this->countryRepository->getByCode('SR');
        $this->countryRepository->setContinent($countrySuriname, $continentAfrica);

        /** @var Country $countrySvalbardAndJanMayen */
        $countrySvalbardAndJanMayen = $this->countryRepository->getByCode('SJ');
        $this->countryRepository->setContinent($countrySvalbardAndJanMayen, $continentEurope);

        /** @var Country $countrySwaziland */
        $countrySwaziland = $this->countryRepository->getByCode('SZ');
        $this->countryRepository->setContinent($countrySwaziland, $continentAfrica);

        /** @var Country $countrySweden */
        $countrySweden = $this->countryRepository->getByCode('SE');
        $this->countryRepository->setContinent($countrySweden, $continentEurope);

        /** @var Country $countrySwitzerland */
        $countrySwitzerland = $this->countryRepository->getByCode('CH');
        $this->countryRepository->setContinent($countrySwitzerland, $continentEurope);

        /** @var Country $countrySyrianArabRepublic */
        $countrySyrianArabRepublic = $this->countryRepository->getByCode('SY');
        $this->countryRepository->setContinent($countrySyrianArabRepublic, $continentAsia);

        /** @var Country $countryTaiwanProvinceOfChina */
        $countryTaiwanProvinceOfChina = $this->countryRepository->getByCode('TW');
        $this->countryRepository->setContinent($countryTaiwanProvinceOfChina, $continentAsia);

        /** @var Country $countryTajikistan */
        $countryTajikistan = $this->countryRepository->getByCode('TJ');
        $this->countryRepository->setContinent($countryTajikistan, $continentAsia);

        /** @var Country $countryTanzaniaUnitedRepublicOf */
        $countryTanzaniaUnitedRepublicOf = $this->countryRepository->getByCode('TZ');
        $this->countryRepository->setContinent($countryTanzaniaUnitedRepublicOf, $continentAfrica);

        /** @var Country $countryThailand */
        $countryThailand = $this->countryRepository->getByCode('TH');
        $this->countryRepository->setContinent($countryThailand, $continentAsia);

        /** @var Country $countryTimorLeste */
        $countryTimorLeste = $this->countryRepository->getByCode('TL');
        $this->countryRepository->setContinent($countryTimorLeste, $continentAsia);

        /** @var Country $countryTogo */
        $countryTogo = $this->countryRepository->getByCode('TG');
        $this->countryRepository->setContinent($countryTogo, $continentAsia);

        /** @var Country $countryTokelau */
        $countryTokelau = $this->countryRepository->getByCode('TK');
        $this->countryRepository->setContinent($countryTokelau, $continentOceania);

        /** @var Country $countryTonga */
        $countryTonga = $this->countryRepository->getByCode('TO');
        $this->countryRepository->setContinent($countryTonga, $continentOceania);

        /** @var Country $countryTrinidadAndTobago */
        $countryTrinidadAndTobago = $this->countryRepository->getByCode('TT');
        $this->countryRepository->setContinent($countryTrinidadAndTobago, $continentNorthAmerica);

        /** @var Country $countryTunisia */
        $countryTunisia = $this->countryRepository->getByCode('TN');
        $this->countryRepository->setContinent($countryTunisia, $continentAfrica);

        /** @var Country $countryTurkey */
        $countryTurkey = $this->countryRepository->getByCode('TR');
        $this->countryRepository->setContinent($countryTurkey, $continentEurope);

        /** @var Country $countryTurkmenistan */
        $countryTurkmenistan = $this->countryRepository->getByCode('TM');
        $this->countryRepository->setContinent($countryTurkmenistan, $continentAsia);

        /** @var Country $countryTurksAndCaicosIslands */
        $countryTurksAndCaicosIslands = $this->countryRepository->getByCode('TC');
        $this->countryRepository->setContinent($countryTurksAndCaicosIslands, $continentNorthAmerica);

        /** @var Country $countryTuvalu */
        $countryTuvalu = $this->countryRepository->getByCode('TV');
        $this->countryRepository->setContinent($countryTuvalu, $continentOceania);

        /** @var Country $countryUganda */
        $countryUganda = $this->countryRepository->getByCode('UG');
        $this->countryRepository->setContinent($countryUganda, $continentAfrica);

        /** @var Country $countryUkraine */
        $countryUkraine = $this->countryRepository->getByCode('UA');
        $this->countryRepository->setContinent($countryUkraine, $continentEurope);

        /** @var Country $countryUnitedArabEmirates */
        $countryUnitedArabEmirates = $this->countryRepository->getByCode('AE');
        $this->countryRepository->setContinent($countryUnitedArabEmirates, $continentAsia);

        /** @var Country $countryUnitedKingdom */
        $countryUnitedKingdom = $this->countryRepository->getByCode('GB');
        $this->countryRepository->setContinent($countryUnitedKingdom, $continentEurope);

        /** @var Country $countryUnitedStates */
        $countryUnitedStates = $this->countryRepository->getByCode('US');
        $this->countryRepository->setContinent($countryUnitedStates, $continentNorthAmerica);

        /** @var Country $countryUnitedStatesMinorOutlyingIslands */
        $countryUnitedStatesMinorOutlyingIslands = $this->countryRepository->getByCode('UM');
        $this->countryRepository->setContinent($countryUnitedStatesMinorOutlyingIslands, $continentOceania);

        /** @var Country $countryUruguay */
        $countryUruguay = $this->countryRepository->getByCode('UY');
        $this->countryRepository->setContinent($countryUruguay, $continentSouthAmerica);

        /** @var Country $countryUzbekistan */
        $countryUzbekistan = $this->countryRepository->getByCode('UZ');
        $this->countryRepository->setContinent($countryUzbekistan, $continentAsia);

        /** @var Country $countryVanuatu */
        $countryVanuatu = $this->countryRepository->getByCode('VU');
        $this->countryRepository->setContinent($countryVanuatu, $continentOceania);

        /** @var Country $countryVenezuela */
        $countryVenezuela = $this->countryRepository->getByCode('VE');
        $this->countryRepository->setContinent($countryVenezuela, $continentSouthAmerica);

        /** @var Country $countryVietNam */
        $countryVietNam = $this->countryRepository->getByCode('VN');
        $this->countryRepository->setContinent($countryVietNam, $continentAsia);

        /** @var Country $countryVirginIslandsBritish */
        $countryVirginIslandsBritish = $this->countryRepository->getByCode('VG');
        $this->countryRepository->setContinent($countryVirginIslandsBritish, $continentNorthAmerica);

        /** @var Country $countryVirginIslandsUS */
        $countryVirginIslandsUS = $this->countryRepository->getByCode('VI');
        $this->countryRepository->setContinent($countryVirginIslandsUS, $continentNorthAmerica);

        /** @var Country $countryWallisAndFutuna */
        $countryWallisAndFutuna = $this->countryRepository->getByCode('WF');
        $this->countryRepository->setContinent($countryWallisAndFutuna, $continentOceania);

        /** @var Country $countryWesternSahara */
        $countryWesternSahara = $this->countryRepository->getByCode('EH');
        $this->countryRepository->setContinent($countryWesternSahara, $continentAfrica);

        /** @var Country $countryYemen */
        $countryYemen = $this->countryRepository->getByCode('YE');
        $this->countryRepository->setContinent($countryYemen, $continentAsia);

        /** @var Country $countryZambia */
        $countryZambia = $this->countryRepository->getByCode('ZM');
        $this->countryRepository->setContinent($countryZambia, $continentAfrica);

        /** @var Country $countryZimbabwe */
        $countryZimbabwe = $this->countryRepository->getByCode('ZW');
        $this->countryRepository->setContinent($countryZimbabwe, $continentAfrica);

        /** @var Country $countrySierraLeone */
        $countrySierraLeone = $this->countryRepository->getByCode('SL');
        $this->countryRepository->setContinent($countrySierraLeone, $continentAfrica);
    }
}
