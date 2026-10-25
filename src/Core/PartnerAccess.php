<?php

declare(strict_types=1);

namespace TCM\Core;

/** Server-side partner scope and capability checks. */
final class PartnerAccess
{
    /** @return array{user:array<string,mixed>,partner:array<string,mixed>,membership:array<string,mixed>} */
    public static function requirePartner(bool $finance = false, bool $activeOnly = false): array
    {
        $user = Auth::user();
        if (
            $user === null
            || ($user['status'] ?? '') !== 'active'
            || !in_array($user['role'] ?? '', ['partner_owner', 'partner_staff'], true)
        ) {
            self::deny('You do not have access to the partner area.');
        }

        $membership = Database::first(
            'SELECT pm.*, p.* FROM partner_memberships pm
             JOIN partners p ON p.id = pm.partner_id
             WHERE pm.user_id = ? AND pm.status = \'active\'
               AND p.status IN (\'agreement_pending\',\'onboarding\',\'active\')
             ORDER BY pm.id LIMIT 1',
            [$user['id']]
        );
        if ($membership === null) {
            self::deny('Your partner account is not active. Contact TCM One for help.');
        }
        if ($activeOnly && $membership['status'] !== 'active') {
            self::deny('This operational feature is available after TCM One activates the partnership. You can update your institution profile while approval is pending.');
        }
        if ($finance && $membership['member_role'] !== 'partner_owner' && (int) $membership['can_view_finance'] !== 1) {
            self::deny('Your account does not have finance access.');
        }

        return ['user' => $user, 'partner' => $membership, 'membership' => $membership];
    }

    private static function deny(string $message): never
    {
        if (Request::isJson()) {
            Response::error($message, 403);
        }
        http_response_code(403);
        echo e($message);
        exit;
    }
}
