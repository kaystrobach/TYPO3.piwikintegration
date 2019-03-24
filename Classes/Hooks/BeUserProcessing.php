<?php

namespace KayStrobach\Piwikintegration\Hooks;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class tx_piwikintegration_Hooks_BeUserProcessing.
 *
 * renews the be user api token after changing the password
 */
class BeUserProcessing
{
    /**
     * Process changes to a backendusers password to generate a new API key for
     * Matomo.
     *
     * @return void
     */
    public function processDatamap_preProcessFieldArray(&$status, $table, $id, &$fieldArray)
    {
        if ($table == 'be_users') {
            if (array_key_exists('password', $status)) {
                $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                    ->getQueryBuilderForTable('be_users');
                $users = $queryBuilder
                    ->select('username', 'pid')
                    ->from('be_users')
                    ->where(
                        $queryBuilder->expr()->eq('uid', (int) $id)
                    )
                    ->execute()
                    ->fetchAll();
                $username = $users[0]['username'];
                $status['tx_piwikintegration_api_code'] = md5($username.$status['password']);
            }
        }
    }
}
