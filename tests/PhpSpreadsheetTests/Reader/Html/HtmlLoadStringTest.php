<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PHPUnit\Framework\TestCase;

class HtmlLoadStringTest extends TestCase
{
    public function testCanLoadFromString(): void
    {
        $html = '<table>
                    <tr>
                        <td>Hello World</td>
                    </tr>
                    <tr>
                        <td>Hello<br />World</td>
                    </tr>
                    <tr>
                        <td>Hello<br>World</td>
                    </tr>
                </table>';
        $spreadsheet = (new Html())->loadFromString($html);
        $firstSheet = $spreadsheet->getSheet(0);

        $cellStyle = $firstSheet->getStyle('A1');
        self::assertFalse($cellStyle->getAlignment()->getWrapText());

        $cellStyle = $firstSheet->getStyle('A2');
        self::assertTrue($cellStyle->getAlignment()->getWrapText());
        $cellValue = $firstSheet->getCell('A2')->getValue();
        self::assertStringContainsString("\n", $cellValue);

        $cellStyle = $firstSheet->getStyle('A3');
        self::assertTrue($cellStyle->getAlignment()->getWrapText());
        $cellValue = $firstSheet->getCell('A3')->getValue();
        self::assertStringContainsString("\n", $cellValue);
    }

    public function testLoadInvalidString(): void
    {
        $this->expectException(ReaderException::class);
        $html = '<table<>';
        $spreadsheet = (new Html())->loadFromString($html);
        $firstSheet = $spreadsheet->getSheet(0);
        $cellStyle = $firstSheet->getStyle('A1');
        self::assertFalse($cellStyle->getAlignment()->getWrapText());
    }

    public function testCanLoadFromStringIntoExistingSpreadsheet(): void
    {
        $html = '<table>
                    <tr>
                        <td>Hello World</td>
                    </tr>
                    <tr>
                        <td>Hello<br />World</td>
                    </tr>
                    <tr>
                        <td>Hello<br>World</td>
                    </tr>
                </table>';
        $reader = new Html();
        $spreadsheet = $reader->loadFromString($html);
        $firstSheet = $spreadsheet->getSheet(0);

        $cellStyle = $firstSheet->getStyle('A1');
        self::assertFalse($cellStyle->getAlignment()->getWrapText());

        $cellStyle = $firstSheet->getStyle('A2');
        self::assertTrue($cellStyle->getAlignment()->getWrapText());
        $cellValue = $firstSheet->getCell('A2')->getValue();
        self::assertStringContainsString("\n", $cellValue);

        $cellStyle = $firstSheet->getStyle('A3');
        self::assertTrue($cellStyle->getAlignment()->getWrapText());
        $cellValue = $firstSheet->getCell('A3')->getValue();
        self::assertStringContainsString("\n", $cellValue);

        $reader->setSheetIndex(1);
        $html = '<table>
                    <tr>
                        <td>Goodbye World</td>
                    </tr>
                </table>';

        self::assertEquals(1, $spreadsheet->getSheetCount());
        $spreadsheet = $reader->loadFromString($html, $spreadsheet);
        self::assertEquals(2, $spreadsheet->getSheetCount());
    }
}
