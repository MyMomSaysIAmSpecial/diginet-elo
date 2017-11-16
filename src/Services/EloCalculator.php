<?php

class EloCalculator
{
    // The maximum possible adjustment per game, called the K-factor,
    // was set at K = 16 for masters and K = 32 for weaker players.
    private $coefficient = 32;
    // Player game result points for win, lose and draw
    private $points = [
        'win' => 1,
        'lose' => 0,
        'draft' => 0.5
    ];
    /**
     * @param int $player first player elo
     * @param int $rival second player elo
     * @param int $result first player game result
     *
     * @return float
     */
    public function calculate($player, $rival, $result)
    {
        $expectedValue = 1 / (1 + 10 ** (($rival - $player) / 400));
        return $player + $this->coefficient * ($this->points[$result] - $expectedValue);
    }
}
