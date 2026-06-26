"""Capture une carte HTML Frogger en PNG (usage ponctuel)."""
from pathlib import Path

from PIL import Image
from playwright.sync_api import sync_playwright

HTML = Path(
    r"C:\MadHackAdemyWebSite\madhackademyWebSite\FicheFormationHtlm\07_Struct_Methodes\ClaudeHtml\Frogger_theme_StrucAndMehtodeCard.html"
)
REF = Path(
    r"C:\MadHackAdemyWebSite\FlashRevisionSoft\SquelletteGCS\images_current\Raylib_Cpp_Basic\0X_Variable.png"
)
OUT = Path(
    r"C:\MadHackAdemyWebSite\FlashRevisionSoft\SquelletteGCS\images_current\Raylib_Cpp_Basic\0x_Struct_Methodes.png"
)
TMP = Path(__file__).resolve().parent / "_tmp_struct_methodes_card.png"

TARGET_W, TARGET_H = Image.open(REF).size


def main() -> None:
    OUT.parent.mkdir(parents=True, exist_ok=True)
    file_url = HTML.resolve().as_uri()

    with sync_playwright() as p:
        browser = p.chromium.launch(channel="msedge", headless=True)
        page = browser.new_page(
            viewport={"width": 420, "height": 620},
            device_scale_factor=2,
        )
        page.goto(file_url, wait_until="networkidle")
        page.wait_for_timeout(800)
        page.locator(".card").screenshot(path=str(TMP), omit_background=True)
        browser.close()

    with Image.open(TMP) as im:
        im = im.convert("RGBA")
        fitted = im.resize((TARGET_W, TARGET_H), Image.Resampling.LANCZOS)
        fitted.save(OUT, "PNG", optimize=True)

    TMP.unlink(missing_ok=True)
    print(f"OK -> {OUT} ({TARGET_W}x{TARGET_H})")


if __name__ == "__main__":
    main()
