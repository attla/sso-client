<?php

namespace Attla\SSO;

trait HasImage
{
    /**
     * Get user image
     *
     * @return string
     */
    public function getImageAttribute()
    {
        if (!empty($this->social_image)) {
            return $this->social_image;
        }

        return $this->gravatar($this->email, 150, $this->defaultImage ?? 'multiavatar');
    }

    /**
     * Get gravatar image by user email
     *
     * @param string $email
     * @param int $size
     * @param string $default
     * @return string
     */
    public function gravatar($email, $size, $default = 'identicon')
    {
        $token = md5(strtolower(trim($email)));

        if ($default == 'multiavatar') {
            $default = 'https://api.multiavatar.com/' . $token . '.png';
        }

        return 'https://s.gravatar.com/avatar/' . $token . '?size=' . $size . '&d=' . $default;
    }
}
