<?php
/**
 * Download Policy Service Test.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Unit\Diagram
 */

namespace WebFalcon\MermaidDiagrams\Tests\Unit\Diagram;

use PHPUnit\Framework\TestCase;
use WebFalcon\MermaidDiagrams\Diagram\Application\Service\DownloadPolicyService;
use WebFalcon\MermaidDiagrams\Diagram\Domain\Diagram;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramDescription;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramId;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramSource;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramStatus;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramTitle;
use WebFalcon\MermaidDiagrams\Diagram\Domain\RenderConfig;
use WebFalcon\MermaidDiagrams\Settings\Infrastructure\SettingsRepository;

/**
 * Unit tests for DownloadPolicyService.
 */
class DownloadPolicyServiceTest extends TestCase {

	public function test_format_filename_sanitizes_title_and_appends_id(): void {
		$filename = DownloadPolicyService::format_filename( 'My Flowchart Diagram!', 123, 'mmd' );
		$this->assertEquals( 'my-flowchart-diagram-123.mmd', $filename );
	}

	public function test_format_filename_uses_fallback_when_title_empty(): void {
		$filename = DownloadPolicyService::format_filename( '', 456, 'svg' );
		$this->assertEquals( 'diagram-456.svg', $filename );
	}
}
