<div>
    <div class="date">{{ $currentDate }}</div>
    <div wire:poll.1s="refreshDate"></div>
    <div class="time">{{ $currentTime }}</div>
    <div wire:poll.1s="refreshTime"></div>
</div>
