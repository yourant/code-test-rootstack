<?php

namespace App\Services\PostalStructure;

use App\Models\AdminLevel1;
use App\Models\AdminLevel2;
use App\Models\AdminLevel3;
use App\Models\Country;
use App\Models\Package;
use App\Models\ZipCode;
use App\Repositories\AdminLevel1Repository;
use App\Repositories\AdminLevel2Repository;
use App\Repositories\AdminLevel3Repository;
use App\Repositories\CountryRepository;
use App\Repositories\PackageRepository;
use App\Repositories\PostalOfficeRepository;
use App\Repositories\ZipCodeRepository;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ClassificationService
{
    /** @var CountryRepository */
    protected $countryRepository;

    /** @var AdminLevel1Repository */
    protected $adminLevel1Repository;

    /** @var AdminLevel2Repository */
    protected $adminLevel2Repository;

    /** @var AdminLevel3Repository */
    protected $adminLevel3Repository;

    /** @var ZipCodeRepository */
    protected $zipCodeRepository;

    /** @var PackageRepository */
    protected $packageRepository;

    /** @var PostalOfficeRepository */
    protected $postalOfficeRepository;

    public function __construct(
        CountryRepository $countryRepository,
        AdminLevel1Repository $adminLevel1Repository,
        AdminLevel2Repository $adminLevel2Repository,
        AdminLevel3Repository $adminLevel3Repository,
        ZipCodeRepository $zipCodeRepository,
        PackageRepository $packageRepository,
        PostalOfficeRepository $postalOfficeRepository
    ) {
        $this->countryRepository = $countryRepository;
        $this->adminLevel1Repository = $adminLevel1Repository;
        $this->adminLevel2Repository = $adminLevel2Repository;
        $this->adminLevel3Repository = $adminLevel3Repository;
        $this->zipCodeRepository = $zipCodeRepository;
        $this->packageRepository = $packageRepository;
        $this->postalOfficeRepository = $postalOfficeRepository;
    }

    /**
     * @param Package $package
     * @return Collection|null
     */
    public function guessZipCodesByPackage(Package $package)
    {
        /** @var Collection $zipCodes */
        $zipCodes = null;
        $country_code = $package->getAgreementServiceDestinationLocationCountryCode();

        if ($country_code == 'MX') { // MEXICO
            $zipCodes = self::guessPackageZipCodesForMexico($country_code, $package->state, $package->city, $package->district, $package->address1, $package->address2, $package->address3,
                $package->zip, $package->location);
        } elseif ($country_code == 'CO') { // COLOMBIA
            $zipCodes = self::guessPackageZipCodesForColombia($country_code, $package->state, $package->city, $package->district, $package->address1, $package->address2, $package->address3,
                $package->zip, $package->location);
        } elseif ($country_code == 'PE') { // PERU
            $zipCodes = self::guessPackageZipCodesForPeru($country_code, $package->state, $package->city, $package->district, $package->address1, $package->address2, $package->address3, $package->zip,
                $package->location);
        } elseif ($country_code == 'CL') { // PERU
            $zipCodes = self::guessPackageZipCodesForChile($country_code, $package->state, $package->city, $package->district, $package->address1, $package->address2, $package->address3,
                $package->zip, $package->location);
        } elseif ($country_code == 'BR') {
            $zipCodes = self::guessPackageZipCodesForBrazil($country_code, $package->state, $package->city, $package->district, $package->address1, $package->address2, $package->address3,
                $package->zip, $package->location);
        }
        return $zipCodes;
    }

    /**
     * @param Package $package
     * @return Collection|null
     */
    public function guessZipCodesByFields($country_code, $state, $town, $township = null, $address1 = null, $address2 = null, $address3 = null, $postal_code = null, $location = null)
    {
        /** @var Collection $zipCodes */
        $zipCodes = null;

        if ($country_code == 'MX') { // MEXICO
            $zipCodes = self::guessPackageZipCodesForMexico($country_code, $state, $town, $township, $address1, $address2, $address3, $postal_code, $location);
        } elseif ($country_code == 'CO') { // COLOMBIA
            $zipCodes = self::guessPackageZipCodesForColombia($country_code, $state, $town, $township, $address1, $address2, $address3, $postal_code, $location);
        } elseif ($country_code == 'PE') { // PERU
            $zipCodes = self::guessPackageZipCodesForPeru($country_code, $state, $town, $township, $address1, $address2, $address3, $postal_code, $location);
        } elseif ($country_code == 'CL') { // PERU
            $zipCodes = self::guessPackageZipCodesForChile($country_code, $state, $town, $township, $address1, $address2, $address3, $postal_code, $location);
        } elseif ($country_code == 'BR') {
//            $zipCodes = self::guessPackageZipCodesForBrazil($package);
        }
        return $zipCodes;
    }

    public function getZipCodesBy($country_code, $state_name, $town_name, $township_name = null)
    {
        /** @var Country $country */
        if (!$country = $this->countryRepository->getByCode($country_code)) {
            throw new Exception("Country {$country_code} not found.");
        }

        /** @var AdminLevel1 $state */
        $state = $country->adminLevels1->first(function ($state) use ($state_name) {
            $request_state_name = strtoupper(strip_accents($state_name));
            $state_name = strtoupper(strip_accents($state->name));
            $state_name_alt = strtoupper(strip_accents($state->name_alt));

            return ($state->name_alt && ($state_name_alt == $request_state_name)) or ($state_name == $request_state_name);
        });
        if (!$state) {
            throw new Exception("State {$state_name} not found for country {$country->name}.");
        }

        /** @var AdminLevel2 $town */
        $town = $state->adminLevels2->filter(function ($town) use ($town_name) {
            return strtoupper(strip_accents($town->name)) == strtoupper(strip_accents($town_name));
        })->first();
        if (!$town) {
            throw new Exception("Town {$town_name} not found for state {$state->name}.");
        }


        $township = null;
        if ($township_name) { // Search Township by name
            /** @var AdminLevel3 $township */
            $township = $town->adminLevels3->filter(function ($township) use ($township_name) {
                return strtoupper(strip_accents($township->name)) == strtoupper(strip_accents($township_name));
            })->first();
        }

        if (!$township) { // Get default Township
            $township = $town->adminLevels3->filter(function ($township) use ($town_name) {
                return strtoupper(strip_accents($township->name)) == strtoupper(strip_accents($town_name));
            })->first();
        }

        if (!$township) {
            throw new Exception("Township {$town_name} not found for town {$town->name}.");
        }

        return $township->zipCodes;
    }

    public function updatePackagePostalStructure(Package $package)
    {
        $zipCodes = $this->guessZipCodesByPackage($package);

        if (!$zipCodes) {
            // Remove both ZipCode and PostalOffice relations
            $this->packageRepository->removeZipCode($package);
            $this->packageRepository->removePostalOffice($package);
        } else {
            /** @var ZipCode $zipCode */
            if ($zipCode = $zipCodes->first()) {
                $postalOffice = null;
                $this->packageRepository->setZipCode($package, $zipCode);

                /** @var Collection $postalOffices */
                $postalOffices = $this->postalOfficeRepository->search(['zip_code_id' => $zipCode->id])->get();

                // Split offices into Mexpost and Regular
                $mexpostOffices = $postalOffices->filter(function ($po, $key) {
                    return preg_match('/mexpost/i', $po->getPostalOfficeTypeName());
                });

                $regularOffices = $postalOffices->reject(function ($po, $key) {
                    return preg_match('/mexpost/i', $po->getPostalOfficeTypeName());
                });

                // Check for MEXPOST first
//                if ($mexpostOffices->isNotEmpty() && $package->isAgreementServicesDestinationLocationCountryMexico() && $package->isAgreementTypePriority()) {
                if ($mexpostOffices->isNotEmpty() && $package->isDistribuitorMexpost()) {
                    $postalOffice = $mexpostOffices->first();
                } else {
                    if ($regularOffices->isNotEmpty()) {
                        $postalOffice = $regularOffices->first();
                    } else {
                        $postalOffice = null;
                    }
                }

                if ($postalOffice) {
                    $this->packageRepository->setPostalOffice($package, $postalOffice);
                } else {
                    $this->packageRepository->removePostalOffice($package);
                }

                return $postalOffice;
            } else {
                $this->packageRepository->removeZipCode($package);
                $this->packageRepository->removePostalOffice($package);
            }
        }

        return null;
    }

    protected function guessPackageZipCodesForMexico($country_code, $state, $town, $township = null, $address1 = null, $address2 = null, $address3 = null, $postal_code = null, $location = null)
    {
        /** @var Collection $zipCodes */
        $zipCodes = collect();

        // First try by zip code
        if ($zip = self::preprocessPostalCodeByCountryCode($postal_code, $country_code)) {
            if ($zc = $this->zipCodeRepository->getByCodeAndCountryCode($zip, $country_code)) {
                $zipCodes->push($zc);
            }

        }

        if ($zipCodes->isEmpty()) {
            // TODO
            // Else, inspect field "location"
            // If location field separated by commas, then follow town / state / township
            // If location field not separated by commas, omit field
            // Else, inspect field "city"
            // If field contains commas, take first component (town), discard the rest
        }

        return $zipCodes;
    }

    protected function guessPackageZipCodesForColombia($country_code, $state, $town, $township = null, $address1 = null, $address2 = null, $address3 = null, $postal_code = null, $location = null)
    {
        /** @var Collection $zipCodes */
        $zipCodes = collect();

        /** @var AdminLevel1 */
        $adminLevel1 = null;

        /** @var AdminLevel2 */
        $adminLevel2 = null;

        /** @var AdminLevel3 */
        $adminLevel3 = null;

        /** @var Country $country */
        $country = Cache::remember("normalization_country_{$country_code}", 60, function () use ($country_code) {
            return $this->countryRepository->getByCode($country_code);
        });

        // Else, inspect field "location"
        if ($location) {
            list($town_name, $state_name) = self::parseFieldWithCommas($location);
            $town_name = self::filterTownExceptionsForColombia($town_name);

            if ($state_name) {
                if ($adminLevel1 = $this->adminLevel1Repository->fuzzySearchByName($state_name, $country)->first()) {
                    if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndAdminLevel1($town_name, $adminLevel1)->first()) {
                        if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($town_name, $adminLevel2)->first()) {
                            return $adminLevel3->zipCodes;
                        }
                    } else {
                        if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndCountry($town_name, $country)->first()) {
                            if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($town_name, $adminLevel2)->first()) {
                                return $adminLevel3->zipCodes;
                            }
                        }
                    }
                }
            }
        }

        // Inspect fields "city"
        $state_name = null;
        $town_name = null;
        if ($town) {
            list($town_name, $state_name) = self::parseFieldWithCommas($town);
            $town_name = self::filterTownExceptionsForColombia($town_name);
            if ($state_name) {
                if ($adminLevel1 = $this->adminLevel1Repository->fuzzySearchByName($state_name, $country)->first()) {
                    if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndAdminLevel1($town_name, $adminLevel1)->first()) {
                        if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($town_name, $adminLevel2)->first()) {
                            return $adminLevel3->zipCodes;
                        }
                    } else {
                        if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndAdminLevel1($state_name, $adminLevel1)->first()) {
                            if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($state_name, $adminLevel2)->first()) {
                                return $adminLevel3->zipCodes;
                            }
                        }
                    }
                }
            } else {
                // One last shot using both "city" and "state"
                list($state_name) = self::parseFieldWithCommas($state);
                if ($state_name) {
                    if ($adminLevel1 = $this->adminLevel1Repository->fuzzySearchByName($state_name, $country)->first()) {
                        if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndAdminLevel1($town_name, $adminLevel1)->first()) {
                            if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($town_name, $adminLevel2)->first()) {
                                return $adminLevel3->zipCodes;
                            }
                        }
                    }
                }
                if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndCountry($town_name, $country)->first()) {
                    if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($town_name, $adminLevel2)->first()) {
                        return $adminLevel3->zipCodes;
                    }
                }
            }
        }

        return $zipCodes;
    }

    protected function guessPackageZipCodesForPeru($country_code, $state, $town, $township = null, $address1 = null, $address2 = null, $address3 = null, $postal_code = null, $location = null)
    {
        /** @var Collection $zipCodes */
        $zipCodes = collect();

        /** @var AdminLevel1 */
        $adminLevel1 = null;

        /** @var AdminLevel2 */
        $adminLevel2 = null;

        /** @var AdminLevel3 */
        $adminLevel3 = null;

        /** @var Country $country */
        $country = Cache::remember("normalization_country_{$country_code}", 60, function () use ($country_code) {
            return $this->countryRepository->getByCode($country_code);
        });

        // Inspect field "location"
        if ($location) {
            list($field1, $field2, $field3) = self::parseFieldWithCommas($location);

            if (!empty($field1) && !empty($field2) && !empty($field3)) {
                $town_name = $field1;
                $state_name = self::filterStateExceptionsForPeru($field2);
                $township_name = $field3;

                if ($adminLevel1 = $this->adminLevel1Repository->fuzzySearchByName($state_name, $country)->first()) {
                    if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndAdminLevel1($town_name, $adminLevel1)->first()) {
                        if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($township_name, $adminLevel2)->first()) {
                            return $adminLevel3->zipCodes;
                        }
                    } else {
                        if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndCountry($town_name, $country)->first()) {
                            if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($township_name, $adminLevel2)->first()) {
                                return $adminLevel3->zipCodes;
                            }
                        }
                    }
                }

            } elseif (!empty($field1) && !empty($field2) && empty($field3)) {
                $town_name = $field1;
                $township_name = $field2;

                if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndCountry($town_name, $country)->first()) {
                    if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($township_name, $adminLevel2)->first()) {
                        return $adminLevel3->zipCodes;
                    }
                }

                $state_name = self::filterStateExceptionsForPeru($field1);
                if ($adminLevel1 = $this->adminLevel1Repository->fuzzySearchByName($state_name, $country)->first()) {
                    if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndAdminLevel1($town_name, $adminLevel1)->first()) {
                        if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($township_name, $adminLevel2)->first()) {
                            return $adminLevel3->zipCodes;
                        }
                    } else {
                        if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndCountry($township_name, $country)->first()) {
                            if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($township_name, $adminLevel2)->first()) {
                                return $adminLevel3->zipCodes;
                            }
                        }
                    }
                }

            }
        }
        // Inspect fields "city"
        if ($town) {
            list($field1, $field2, $field3) = self::parseFieldWithCommas($town);

            if (!empty($field1) && !empty($field2) && !empty($field3)) {
                $town_name = $field1;
                $state_name = self::filterStateExceptionsForPeru($field2);
                $township_name = $field3;

                if ($adminLevel1 = $this->adminLevel1Repository->fuzzySearchByName($state_name, $country)->first()) {
                    if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndAdminLevel1($town_name, $adminLevel1)->first()) {
                        if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($township_name, $adminLevel2)->first()) {
                            return $adminLevel3->zipCodes;
                        }
                    } else {
                        if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndCountry($town_name, $country)->first()) {
                            if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($township_name, $adminLevel2)->first()) {
                                return $adminLevel3->zipCodes;
                            }
                        }
                    }
                }
            } elseif (!empty($field1) && !empty($field2) && empty($field3)) {
                $town_name = $field1;
                $township_name = $field2;
                if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndCountry($town_name, $country)->first()) {
                    if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($township_name, $adminLevel2)->first()) {
                        return $adminLevel3->zipCodes;
                    }
                }
                list($state_name) = self::parseFieldWithCommas($state);
                $state_name = self::filterStateExceptionsForPeru($state_name);
                if ($state_name) {
                    if ($adminLevel1 = $this->adminLevel1Repository->fuzzySearchByName($state_name, $country)->first()) {
                        $town_name = [$field1, $field2];
                        if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndAdminLevel1($town_name, $adminLevel1)->first()) {
                            if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($town_name, $adminLevel2)->first()) {
                                return $adminLevel3->zipCodes;
                            }
                        }
                    }
                }

                if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndCountry($township_name, $country)->first()) {
                    if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($town_name, $adminLevel2)->first()) {
                        return $adminLevel3->zipCodes;
                    }
                }

            } elseif (!empty($field1) && empty($field2) && empty($field3)) {
                $town_name = $field1;
                list($state_name) = self::parseFieldWithCommas($state);
                $state_name = self::filterStateExceptionsForPeru($state_name);

                if ($state_name) {
                    if ($adminLevel1 = $this->adminLevel1Repository->fuzzySearchByName($state_name, $country)->first()) {
                        if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndAdminLevel1($town_name, $adminLevel1)->first()) {
                            if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($town_name, $adminLevel2)->first()) {
                                return $adminLevel3->zipCodes;
                            }
                        }

                        if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel1($town_name, $adminLevel1)->first()) {
                            return $adminLevel3->zipCodes;
                        }
                    } else {
                        if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndCountry($town_name, $country)->first()) {
                            if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($town_name, $adminLevel2)->first()) {
                                return $adminLevel3->zipCodes;
                            }
                        }
                    }
                } else {
                    if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndCountry($town_name, $country)->first()) {
                        if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($town_name, $adminLevel2)->first()) {
                            return $adminLevel3->zipCodes;
                        }
                    }
                }
            }
        }

        // Inspect "district" field
        if ($township) {
            list($field1, $field2, $field3) = self::parseFieldWithCommas($township);

            if (!empty($field1) && !empty($field2) && !empty($field3)) {
                $town_name = $field1;
                $state_name = self::filterStateExceptionsForPeru($field2);
                $township_name = $field3;

                if ($adminLevel1 = $this->adminLevel1Repository->fuzzySearchByName($state_name, $country)->first()) {
                    if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndAdminLevel1($town_name, $adminLevel1)->first()) {
                        if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($township_name, $adminLevel2)->first()) {
                            return $adminLevel3->zipCodes;
                        }
                    } else {
                        if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndCountry($town_name, $country)->first()) {
                            if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($township_name, $adminLevel2)->first()) {
                                return $adminLevel3->zipCodes;
                            }
                        }
                    }
                }

            } elseif (!empty($field1) && !empty($field2) && empty($field3)) {
                $town_name = $field1;
                $township_name = $field2;

                if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndCountry($town_name, $country)->first()) {
                    if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($township_name, $adminLevel2)->first()) {
                        return $adminLevel3->zipCodes;
                    }
                }

                $state_name = self::filterStateExceptionsForPeru($field1);
                if ($adminLevel1 = $this->adminLevel1Repository->fuzzySearchByName($state_name, $country)->first()) {
                    if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndAdminLevel1($town_name, $adminLevel1)->first()) {
                        if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($township_name, $adminLevel2)->first()) {
                            return $adminLevel3->zipCodes;
                        }
                    } else {
                        if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndCountry($township_name, $country)->first()) {
                            if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($township_name, $adminLevel2)->first()) {
                                return $adminLevel3->zipCodes;
                            }
                        }
                    }
                }

            }
        }

        // Lastly try by zip code
        if ($zip = self::preprocessPostalCodeByCountryCode($postal_code, $country_code)) {
            /** @var ZipCode $zc */
            if ($zc = $this->zipCodeRepository->getByCodeAndCountryCode($zip, $country_code)) {
                $zipCodes->push($zc);

                return $zipCodes;
            }
        }
        return $zipCodes;
    }

    protected function guessPackageZipCodesForChile($country_code, $state, $town, $township = null, $address1 = null, $address2 = null, $address3 = null, $postal_code = null, $location = null)
    {
        /** @var Collection $zipCodes */
        $zipCodes = collect();

        /** @var AdminLevel1 */
        $adminLevel1 = null;

        /** @var AdminLevel2 */
        $adminLevel2 = null;

        /** @var AdminLevel3 */
        $adminLevel3 = null;

        /** @var Country $country */
        $country = Cache::remember("normalization_country_{$country_code}", 60, function () use ($country_code) {
            return $this->countryRepository->getByCode($country_code);
        });

        // Inspect fields "city"
        list($field1, $field2, $field3) = self::parseFieldWithCommas($town);

        if (!empty($field1)) {
            $township_name = $field1;

            if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndCountry($township_name, $country)->first()) {
                return $adminLevel3->zipCodes;
            }
        }

        if (!empty($field2)) {
            $township_name = $field2;

            if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndCountry($township_name, $country)->first()) {
                return $adminLevel3->zipCodes;
            }
        }

        if (!empty($address2)) {
            $township_name = $address2;

            if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndCountry($township_name, $country)->first()) {
                return $adminLevel3->zipCodes;
            }
        }

        // Lastly try by zip code
        if ($zip = self::preprocessPostalCodeByCountryCode($postal_code, $country_code)) {
            /** @var ZipCode $zc */
            if ($zc = $this->zipCodeRepository->getByCodeAndCountryCode($zip, $country_code)) {
                $zipCodes->push($zc);

                return $zipCodes;
            }
        }

        return $zipCodes;
    }

    protected function guessPackageZipCodesForBrazil($country_code, $state, $town, $township = null, $address1 = null, $address2 = null, $address3 = null, $postal_code = null, $location = null)
    {
        /** @var Collection $zipCodes */
        $zipCodes = collect();

        /** @var AdminLevel1 */
        $adminLevel1 = null;

        /** @var AdminLevel2 */
        $adminLevel2 = null;

        /** @var AdminLevel3 */
        $adminLevel3 = null;

        /** @var Country $country */
        $country = Cache::remember("normalization_country_{$country_code}", 60, function () use ($country_code) {
            return $this->countryRepository->getByCode($country_code);
        });

        list($state_name) = self::parseFieldWithCommas($state);
        if ($state_name) {
            if ($adminLevel1 = $this->adminLevel1Repository->fuzzySearchByName($state_name, $country)->first()) {
                list($town_name) = self::parseFieldWithCommas($town);
                if ($adminLevel2 = $this->adminLevel2Repository->fuzzySearchByNameAndAdminLevel1($town_name, $adminLevel1)->first()) {
                    if ($adminLevel3 = $this->adminLevel3Repository->fuzzySearchByNameAndAdminLevel2($town_name, $adminLevel2)->first()) {
                        return $adminLevel3->zipCodes;
                    }
                }
            }
        }

        // Lastly try by zip code
        if ($zip = self::preprocessPostalCodeByCountryCode($postal_code, $country_code)) {
            /** @var ZipCode $zc */
            if ($zc = $this->zipCodeRepository->getByCodeAndCountryCode($zip, $country_code)) {
                $zipCodes->push($zc);

                return $zipCodes;
            }
        }

        return $zipCodes;
    }

    private function parseFieldWithCommas($input)
    {
        $field1 = null;
        $field2 = null;
        $field3 = null;
        if (mb_strpos($input, ',') or mb_strpos($input, '-') or mb_strpos($input, '_') or mb_strpos($input, '/')) {
            $parts = preg_split("/,|-|\//", $input);
            $field1 = isset($parts[0]) ? trim($parts[0]) : null;
            $field2 = isset($parts[1]) ? trim($parts[1]) : null;
            $field3 = isset($parts[2]) ? trim($parts[2]) : null;
        } else {
            $field1 = trim($input);
        }
        return [$field1, $field2, $field3];
    }

    private function filterTownExceptionsForColombia($input)
    {
        $exceptions = [
            'cerrito valle'                 => 'el cerrito',
            'san jose de cucuta'            => 'cucuta',
            'pereira cuba'                  => 'pereira',
            'medellin barrio la castellana' => 'medellin',
            'bogota barrio ferias'          => 'bogota',
            'cali valle'                    => 'cali',
            'cali colombia'                 => 'cali',
            'envigado antioquia'            => 'envigado',
            'vijes valle'                   => 'vijes',
            'cartganega'                    => 'cartagena',
            'corregimiento de yarima'       => ['san vicente de churui', 'yarima'],
            'arboleda berruecos'            => ['arboleda', 'berruecos']
        ];

        $input = strtolower(strip_accents($input));
        if (array_key_exists($input, $exceptions)) {
            return $exceptions[$input];
        }
        return $input;
    }

    private function filterStateExceptionsForPeru($input)
    {
        $exceptions = [
            'el callao' => 'callao',
        ];

        $input = strtolower(strip_accents($input));
        if (array_key_exists($input, $exceptions)) {
            return $exceptions[$input];
        }
        return $input;
    }

    private function preprocessPostalCodeByCountryCode($postal_code, $country_code)
    {
        if ($country_code == 'MX') {
            if (!ctype_digit($postal_code) && !is_int($postal_code)) {
                // Must be digits only
                return null;
            }

            return str_pad($postal_code, 5, '0', STR_PAD_LEFT);
        } elseif ($country_code == 'CO') {
            if (!ctype_digit($postal_code) && !is_int($postal_code)) {
                // Must be digits only
                return null;
            }

            if (preg_match('/^99970|^000|^11111/', $postal_code)) {
                // Invalid zip codes
                return null;
            }

            if (mb_strlen($postal_code) < 6) {
                // Zip code must be at least 6 digits
                return null;
            }

            return $postal_code;
        } elseif ($country_code == 'PE') {
            if (!ctype_digit($postal_code) && !is_int($postal_code)) {
                // Must be digits only
                return null;
            }

            if (preg_match('/^999|^000|^11111/', $postal_code)) {
                // Invalid zip codes
                return null;
            }

            if (mb_strlen($postal_code) != 6) {
                // Zip code must be 6 digits
                return null;
            }

            return $postal_code;
        } elseif ($country_code == 'CL') {
            if (!ctype_digit($postal_code) && !is_int($postal_code)) {
                // Must be digits only
                return null;
            }

            if (preg_match('/^00|^11111/', $postal_code)) {
                // Invalid zip codes
                return null;
            }

            if (mb_strlen($postal_code) != 7) {
                // Zip code must be 7 digits
                return null;
            }

            return $postal_code;
        } elseif ($country_code == 'BR') {
            if (!ctype_digit($postal_code) && !is_int($postal_code)) {
                // Must be digits only
                return null;
            }

            if (mb_strlen($postal_code) != 8) {
                // Zip code must be 7 digits
                return null;
            }

            return substr($postal_code, 0, 5);
        }

        return null;
    }

}