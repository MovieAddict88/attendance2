import re
from playwright.sync_api import sync_playwright, expect

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    try:
        # 1. Navigate to the teacher portal which is the login page
        page.goto("http://127.0.0.1:8000/teacher/", timeout=60000)

        # 2. Login as the teacher
        expect(page).to_have_title("Teacher Login")
        page.fill("input[name='email']", "teacher@test.com")
        page.fill("input[name='password']", "password")

        # Click login and wait for navigation to dashboard
        with page.expect_navigation(url=re.compile(r".*/teacher/dashboard.php"), wait_until="load"):
            page.click("button[type='submit']")

        print("Successfully logged in as teacher.")

        # 3. Navigate to the Manage Class page
        page.goto("http://127.0.0.1:8000/teacher/manage_class.php?section_id=1&subject_id=1", timeout=60000)

        # Wait for the student 'Test Student' to be visible
        expect(page.locator("text=Test Student")).to_be_visible(timeout=60000)
        print("Navigated to the correct class management page.")

        # 4. Mark attendance for the student, waiting for network responses
        student_row = page.locator("tr", has=page.locator("text=Test Student"))
        attendance_cells = student_row.locator(".attendance-cell")

        # Get locators for totals
        present_total_locator = student_row.locator("#present-1")
        absent_total_locator = student_row.locator("#absent-1")
        remarks_locator = student_row.locator("#remarks-1")

        # Click Monday - Present
        with page.expect_response("**/update_attendance.php") as response_info:
            attendance_cells.nth(0).click()
        expect(present_total_locator).to_have_text("1")

        # Click Tuesday - Present
        with page.expect_response("**/update_attendance.php") as response_info:
            attendance_cells.nth(1).click()
        expect(present_total_locator).to_have_text("2")

        # Click Wednesday - First to Present
        with page.expect_response("**/update_attendance.php") as response_info:
            attendance_cells.nth(2).click()
        expect(present_total_locator).to_have_text("3")

        # Click Wednesday again - to Absent
        with page.expect_response("**/update_attendance.php") as response_info:
            attendance_cells.nth(2).click()
        expect(absent_total_locator).to_have_text("1")
        expect(present_total_locator).to_have_text("2")

        # 5. Verify the remarks calculation
        # 2 days present out of 5 school days = 40%
        expect(remarks_locator).to_have_text("40.00%")
        print("Remarks column verified successfully.")

        # 6. Take a screenshot
        screenshot_path = "jules-scratch/verification/verification.png"
        page.screenshot(path=screenshot_path)
        print(f"Screenshot saved to {screenshot_path}")

    except Exception as e:
        print(f"An error occurred: {e}")
        page.screenshot(path="jules-scratch/verification/error.png")

    finally:
        browser.close()

with sync_playwright() as playwright:
    run(playwright)