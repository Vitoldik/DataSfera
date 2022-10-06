<?php

namespace App\Actions;

use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use App\Contracts\GetLeadActionContract;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Lead;
use App\Services\AmoCRM;
use App\Utilities\StringUtils;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class GetLeadActionAction implements GetLeadActionContract {

    public function __invoke(Request $request) {
        try {
            $token = AmoCRM::instance()->getTokenFromEncryptedCookie();

            if ($token) {
                $accessToken = AmoCRM::instance()->getAccessTokenFromCookie($token);
                AmoCRM::instance()->setApiCredentials($token->baseDomain, $accessToken);

                Company::query()->delete();
                Lead::query()->delete();
                Contact::query()->delete();

                // Выкачиваем сделки
                $leads = AmoCRM::instance()->getApiClient()->leads()->get();
                $leadCounter = 0;

                foreach ($leads as $value) {
                    $lead = $value->toArray();
                    $lead['custom_fields_values'] = json_encode($lead['custom_fields_values']);

                    if (array_key_exists('company', $lead)) {
                        $companyId = $lead['company']['id'];
                        $lead['company_id'] = $companyId;
                        unset($lead['company']);
                    }

                    try {
                        if (Lead::query()->create($lead)->save())
                            $leadCounter++;
                    } catch (QueryException $ex) {
                        error_log('Ошибка при выкачке сделок: ' . $ex->getMessage());
                    }
                }

                // Выкачиваем компании
                $companies = AmoCRM::instance()->getApiClient()->companies()->get();
                $companyCounter = 0;

                foreach ($companies as $value) {
                    $company = $value->toArray();

                    if (!Lead::query()->where('company_id', $company['id'])->exists()) // Исключаем компании без сделок
                        continue;

                    $company['custom_fields_values'] = json_encode($company['custom_fields_values']);

                    try {
                        if (Company::query()->create($company)->save())
                            $companyCounter++;
                    } catch (QueryException $ex) {
                        error_log('Ошибка при выкачке компаний: ' . $ex->getMessage());
                    }
                }

                // Выкачиваем контакты
                $contacts = AmoCRM::instance()->getApiClient()->contacts()->get();
                $contactCounter = 0;

                foreach ($contacts as $value) {
                    $contact = $value->toArray();
                    $companyId = $contact['company']['id'];

                    if (!Lead::query()->where('company_id', $companyId)->exists()) // Исключаем контакты без сделок
                        continue;

                    $contact['company_id'] = $companyId;
                    unset($contact['company']);
                    $contact['custom_fields_values'] = json_encode($contact['custom_fields_values']);

                    try {
                        if (Contact::query()->create($contact)->save())
                            $contactCounter++;
                    } catch (QueryException $ex) {
                        error_log('Ошибка при выкачке контактов: ' . $ex->getMessage());
                    }
                }

                $decLead = StringUtils::getDeclinedString($leadCounter, ['сделка', 'сделки', 'сделок']);
                $decCompany = StringUtils::getDeclinedString($companyCounter, ['компания', 'компании', 'компаний']);
                $decContact = StringUtils::getDeclinedString($contactCounter, ['контакт', 'контакта', 'контактов']);


                return view('unloaded_completed', [
                    'result' => "🚀 Успешно выгружено $decLead, $decCompany и $decContact!"
                ]);
            }
        } catch (AmoCRMApiException|AmoCRMMissedTokenException|AmoCRMoAuthApiException $ex) {
            $errorMessage = $ex->getMessage();

            return view('unloaded_completed', [
                'result' => "Ошибка выгрузки из CRM: $errorMessage. Обратитесь к администратору!"
            ]);
        }

        return view('unloaded_completed', [
            'result' => "Ошибка выгрузки из CRM, обратитесь к администратору!"
        ]);
    }
}
