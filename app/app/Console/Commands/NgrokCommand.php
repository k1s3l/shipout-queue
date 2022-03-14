<?php

namespace App\Console\Commands;

use App\Events\TunnelCreated;
use Illuminate\Console\Command;
use JnJairo\Laravel\Ngrok\NgrokCommand as NgrokDefaultCommand;
use Symfony\Component\Process\Process;

class NgrokCommand extends NgrokDefaultCommand
{
    protected $signature = 'ngrok_event
                            {host-header? : Host header to identify the app (Example: myapp.test)}
                            {--H|host= : Host to tunnel the requests (default: localhost)}
                            {--P|port= : Port to tunnel the requests (default: 80)}
                            {--E|extra=* : Extra arguments to ngrok command}';

    /**
     * Run the process.
     *
     * @param \Symfony\Component\Process\Process $process
     * @return int Exit code.
     */
    protected function runProcess(Process $process) : int
    {
        $webService = $this->webService;

        $webServiceStarted = false;
        $tunnelStarted = false;

        $process->run(function ($type, $data) use (&$process, &$webService, &$webServiceStarted, &$tunnelStarted) {
            if (! $webServiceStarted) {
                if (preg_match('/msg="starting web service".*? addr=(?<addr>\S+)/', $process->getOutput(), $matches)) {
                    $webServiceStarted = true;

                    $webServiceUrl = 'http://' . $matches['addr'];

                    $webService->setUrl($webServiceUrl);

                    $this->line('<fg=green>Web Interface: </fg=green>' . $webServiceUrl . "\n");
                }
            }

            if ($webServiceStarted && ! $tunnelStarted) {
                $tunnels = $webService->getTunnels();

                event(new TunnelCreated($tunnels));

                if (! empty($tunnels) && count($tunnels) > 1) {
                    $tunnelStarted = true;

                    foreach ($tunnels as $tunnel) {
                        $this->line('<fg=green>Forwarding: </fg=green>'
                            . $tunnel['public_url'] . ' -> ' . $tunnel['config']['addr']);
                    }
                }
            }

            if (Process::OUT === $type) {
                $process->clearOutput();
            } else {
                $this->error($data);
                $process->clearErrorOutput();
            }
        });

        $this->error($process->getErrorOutput());

        return $process->getExitCode();
    }
}
