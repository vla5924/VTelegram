<?php

namespace VTg\Tools;

/**
 * @brief Class is just for getting quick access to popular emoji
 * @details This can be useful if you want to insert some 'tool' icons to your buttons or messages
 */
class Icon
{
    /**
     * @name Attention
     */
    ///@{
    const info = 'ℹ️'; ///< Info
    const warning = '⚠️'; ///< Warning
    const ban = '🚫'; ///< Ban (block)
    ///@}

    /**
     * @name Arrows
     */
    ///@{
    const left = '⬅️'; ///< Left arrow
    const right = '➡️'; ///< Right arrow
    const up = '⬆️'; ///< Up arrow
    const down = '⬇️'; ///< Down arrow
    ///@}

    /**
     * @name Arrows (caret style)
     */
    ///@{
    const left_a = '◀️'; ///< Left arrow
    const right_a = '▶️'; ///< Right arrow
    const up_a = '🔼'; ///< Up arrow
    const down_a = '🔽'; ///< Down arrow
    ///@}

    /**
     * @name Miscellaneous
     */
    ///@{
    const fire = '🔥'; ///< Fire
    const bomb = '💣'; ///< Bomb
    const trash = '🗑'; ///< Trash (recycle bin)
    const globe = '🌐'; ///< Globe (earth sign)
    const box = '📦'; ///< Box (archive)
    ///@}

    /**
     * @name Checking marks
     */
    ///@{
    const tick = '✔️'; ///< Tick
    const tick_g = '✅'; ///< Green tick
    const tick_b = '☑️'; ///< Alternative tick
    const cross = '❌'; ///< Cross
    const cross_g = '❎'; ///< Green cross
    const cross_b = '✖️'; ///< Alternative cross
    ///@}

    /**
     * @name Playback controls
     */
    ///@{
    const play = '▶️'; ///< Play
    const pause = '⏸'; ///< Pause
    const play_pause = '⏯'; ///< Play/pause
    const refresh = '🔄'; ///< Refresh (update, sync)
    ///@}

    /**
     * @name Writing and attachments
     */
    ///@{
    const pencil = '✏️'; ///< Pencil
    const pen = '🖊'; ///< Pen
    const writing = '✍️'; ///< Hand writing
    const clip = '📎'; ///< Clip (attach)
    const link = '🔗'; ///< Link (chain)
    const file = '📄'; ///< File (list)
    const folder = '📁'; ///< Folder
    ///@}
}
