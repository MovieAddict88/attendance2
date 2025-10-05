from playwright.sync_api import sync_playwright, expect
import re

def run_verification(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    try:
        # 1. Login as teacher
        page.goto("http://0.0.0.0:8000/teacher/index.php")
        page.get_by_label("Email").fill("teacher@example.com")
        page.get_by_label("Password").fill("password123")
        page.get_by_role("button", name="Login").click()

        # Wait for navigation to the dashboard
        expect(page).to_have_url("http://0.0.0.0:8000/teacher/dashboard.php")
        print("Successfully logged in as teacher.")

        # 2. Navigate to the manage class page by clicking the first assignment card
        page.locator(".assignment-card a").first.click()

        # Wait for navigation to the manage class page using a regex
        expect(page).to_have_url(re.compile(".*manage_class.php.*"))
        print("Navigated to the manage class page.")

        # 3. Verify the student's name and sex
        student_name_cell = page.locator("td.name-col")
        # Use a regex to be more robust against whitespace issues
        expect(student_name_cell).to_have_text(re.compile(r"Torrejos,\s+Roel\s+Lim"))
        print("Student name format is correct.")

        # The sex is in the immediate next `td` element. Use first() to avoid strict mode violation.
        sex_cell = student_name_cell.locator("xpath=./following-sibling::td").first
        expect(sex_cell).to_have_text("M")
        print("Student sex is correct.")

        # 4. Take a screenshot
        screenshot_path = "jules-scratch/verification/attendance_dashboard.png"
        page.locator(".sf2-sheet").screenshot(path=screenshot_path)
        print(f"Screenshot saved to {screenshot_path}")

    except Exception as e:
        print(f"An error occurred: {e}")
        page.screenshot(path="jules-scratch/verification/error.png")
    finally:
        browser.close()

with sync_playwright() as playwright:
    run_verification(playwright)