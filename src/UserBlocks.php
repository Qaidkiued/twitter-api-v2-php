<?php

namespace Noweh\TwitterApi;

/**
 * Class User/Blocks Controller
 * @see <a href="https://developer.twitter.com/en/docs/twitter-api/users/blocks/api-reference/get-users-blocking">Blocks</a>
 * @author Martin Zeitler
 */
class UserBlocks extends AbstractController {

    public const MODES = [
        'LOOKUP' => 'lookup',
        'BLOCK' => 'block',
        'UNBLOCK' => 'unblock'
    ];

    private int $target_user_id;

    /**
     * @param array<int, string> $settings
     * @throws \Exception
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        if (!isset($settings['account_id'])) {
            throw new \Exception('Incomplete settings passed. Expected "account_id"');
        }

        $this->setAuthMode(1);
        $this->setEndpoint('users/'.$this->account_id.'/blocking');
    }

    /**
     * Look up blocked users.
     * @return UserBlocks
     */
    public function lookup(): UserBlocks
    {
        $this->mode = self::MODES['LOOKUP'];
        return $this;
    }

    /**
     * Block user by username or ID.
     * @return UserBlocks
     */
    public function block(): UserBlocks
    {
        $this->setHttpRequestMethod('POST');
        $this->mode = self::MODES['BLOCK'];
        return $this;
    }

    /**
     * Unblock user by username or ID.
     *
     * @param int $user_id
     * @return UserBlocks
     */
    public function unblock(int $user_id): UserBlocks
    {
        $this->setHttpRequestMethod('DELETE');
        $this->mode = self::MODES['UNBLOCK'];
        $this->target_user_id = $user_id;
        return $this;
    }

    /**
     * Retrieve Endpoint value and rebuilt it with the expected parameters
     * @return string the URL for the request.
     * @throws \Exception
     */
    protected function constructEndpoint(): string {

        $endpoint = parent::constructEndpoint();
        if ($this->mode == self::MODES['UNBLOCK']) {
            $endpoint .= '/'.$this->target_user_id;
        }

        // Pagination
        if (! is_null($this->next_page_token)) {
            $endpoint .= '?pagination_token=' . $this->next_page_token;
        }

        return $endpoint;
    }
}
