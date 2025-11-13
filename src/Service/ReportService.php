<?php

namespace App\Service;

use App\Entity\Payment;
use App\Entity\Request;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Сервис для выборок, используемых в разделe отчётов.
 */
final class ReportService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @return array<int, array{equipment: string, request: string, customer: string, sum: int}>
     */
    public function getDebitorka(User $user, bool $isAdmin): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('equipment.name AS equipmentName')
            ->addSelect('request.name AS requestName')
            ->addSelect('customer.name AS customerName')
            ->addSelect('COALESCE(SUM(payment.sum), 0) AS balance')
            ->from(Payment::class, 'payment')
            ->join('payment.request', 'request')
            ->join('request.customer', 'customer')
            ->join('request.equipment', 'equipment')
            ->groupBy('equipment.id')
            ->addGroupBy('request.id')
            ->addGroupBy('customer.id')
            ->having('SUM(payment.sum) < 0')
            ->orderBy('customer.name', 'ASC')
            ->addOrderBy('request.name', 'ASC');

        if (!$isAdmin) {
            $qb->andWhere('customer.creator = :user')
                ->setParameter('user', $user);
        }

        $rows = $qb->getQuery()->getResult();

        return array_map(static function (array $row): array {
            $balance = (int) ($row['balance'] ?? 0);

            return [
                'equipment' => (string) $row['equipmentName'],
                'request' => (string) $row['requestName'],
                'customer' => (string) $row['customerName'],
                'sum' => intdiv($balance, 100),
            ];
        }, $rows);
    }

    /**
     * @return array<int, array{customer: string, equipment: string, request: string, sum: int}>
     */
    public function getTotalPayed(User $user, bool $isAdmin): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('customer.name AS customerName')
            ->addSelect('equipment.name AS equipmentName')
            ->addSelect('request.name AS requestName')
            ->addSelect('COALESCE(SUM(CASE WHEN payment.sum > 0 THEN payment.sum ELSE 0 END), 0) AS positiveTotal')
            ->from(Payment::class, 'payment')
            ->join('payment.request', 'request')
            ->join('request.customer', 'customer')
            ->join('request.equipment', 'equipment')
            ->where('request.status IN (:statuses)')
            ->setParameter('statuses', [Request::STATUS_NEW, Request::STATUS_IN_PROGRESS])
            ->groupBy('customer.id')
            ->addGroupBy('equipment.id')
            ->addGroupBy('request.id')
            ->having('SUM(CASE WHEN payment.sum > 0 THEN payment.sum ELSE 0 END) > 0')
            ->orderBy('customer.name', 'ASC')
            ->addOrderBy('request.name', 'ASC');

        if (!$isAdmin) {
            $qb->andWhere('customer.creator = :user')
                ->setParameter('user', $user);
        }

        $rows = $qb->getQuery()->getResult();

        return array_map(static function (array $row): array {
            $total = (int) ($row['positiveTotal'] ?? 0);

            return [
                'customer' => (string) $row['customerName'],
                'equipment' => (string) $row['equipmentName'],
                'request' => (string) $row['requestName'],
                'sum' => intdiv($total, 100),
            ];
        }, $rows);
    }

    /**
     * @return array<int, array{month: string, sum: int}>
     */
    public function getSalaryByMonth(User $user, bool $isAdmin): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select("FUNCTION('DATE_FORMAT', payment.created, '%Y-%m') AS monthLabel")
            ->addSelect('COALESCE(SUM(CASE WHEN payment.sum > 0 THEN payment.sum ELSE 0 END), 0) AS totalPositive')
            ->from(Payment::class, 'payment')
            ->join('payment.request', 'request')
            ->join('request.customer', 'customer')
            ->groupBy('monthLabel')
            ->orderBy('monthLabel', 'ASC');

        if (!$isAdmin) {
            $qb->andWhere('customer.creator = :user')
                ->setParameter('user', $user);
        }

        $rows = $qb->getQuery()->getResult();

        return array_map(static function (array $row): array {
            $total = (int) ($row['totalPositive'] ?? 0);

            return [
                'month' => (string) $row['monthLabel'],
                'sum' => intdiv($total, 100),
            ];
        }, $rows);
    }
}
