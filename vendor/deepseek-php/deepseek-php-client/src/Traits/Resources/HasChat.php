<?php

namespace DeepSeek\Traits\Resources;

use DeepSeek\Resources\Chat;

trait HasChat
{
    /**
     * Send the accumulated queries to the Chat resource.
     *
     * @return string
     */
    public function chat(): string
    {
        $requestData = [
            'messages' => $this->queries,
            'model' => $this->model,
            'stream' => $this->stream,
        ];
        $this->queries = [];
        $this->setResult((new Chat($this->httpClient))->sendRequest($requestData));
        return $this->getResult()->getContent();
    }
}
