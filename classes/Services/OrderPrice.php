<?php


class OrderPrice
{
    public function getTotalPaidByReference($reference)
    {
        $total = 0;
        try {
            $orderCollection = Order::getByReference($reference);
        } catch (Exception $e) {
            return $total;
        }

        $orders = $orderCollection->getResults();

        if (empty($orders)) {
            return $total;
        }

        foreach ($orders as $order) {
            $total += $order->total_paid;
        }

        return $total;
    }
}